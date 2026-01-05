<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Apartment;
use App\Models\Booking;
use App\Notifications\ApprovedNotification;
use App\Notifications\RejectNotification;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class BookingController extends Controller
{
    protected $bookingService;
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }
    //show all bookings
    public function index(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString(); //  تاريخ اليوم

        //كلشي حجوزات بتبدا  من اليوم وللايام القادمة
        $currentBookings = Booking::where('renter_id', $user->id)
            ->where('status', 'approved')
            ->where('start_date', '>=', $today)
            ->with('apartment:id,apartment_name,city,images') // نختار الحقول التي نحتاجها
            ->latest('start_date') // نرتبها حسب تاريخ البدء الأقرب
            ->get();

        //حجوزات انتهت
        $previousBookings = Booking::where('renter_id', $user->id)
            ->where('status', 'approved')
            ->where('end_date', '<', $today)
            ->with('apartment:id,apartment_name,city,images')
            ->latest()
            ->get();

        //  جلب الحجوزات الملغاة (Canceled)
        // 'canceled' ,'rejected'
        $canceledBookings = Booking::where('renter_id', $user->id)
            ->whereIn('status', ['canceled', 'rejected'])
            ->with('apartment:id,apartment_name,city,images')
            ->latest()
            ->get();

        //جلب الحجوزات  قيد الانتظار
        $pendingBookings = Booking::where('renter_id', $user->id)
            ->where('status', 'pending')
            ->with('apartment:id,apartment_name,city,images')
            ->latest()
            ->get();

        return response()->json([
            'current' => $currentBookings,
            'previous' => $previousBookings,
            'canceled' => $canceledBookings,
            'pending' => $pendingBookings,
        ]);
    }




    public function store(StoreBookingRequest $request)
    {
        $validatedData = $request->validated();
        $apartmentId = $validatedData['apartment_id'];
        $renter = Auth::user()->id;

        try {
            return DB::transaction(function () use ($validatedData, $apartmentId, $renter) {
                $isBooked = Booking::where('apartment_id', $apartmentId)
                    ->whereNotIn('status', ['rejected', 'canceled', 'completed', 'pending'])
                    ->where(function ($query) use ($validatedData) {
                        $query->where('start_date', '<=', $validatedData['end_date'])
                            ->where('end_date', '>=', $validatedData['start_date']);
                    })

                    ->exists();
                    if ($isBooked) {
                        throw new \Exception('يوجد حجز مؤكد متعارض في نفس الفترة.');
                    }
                $booking = Booking::create(['renter_id' => $renter,'total_price'=> 500] + $validatedData);
                return response()->json([
                    'message' => 'Booking request created successfully.',
                    'booking' => $booking
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'لا يمكنك الحجز في هذا الوقت، يوجد حجز مؤكد متعارض.'
            ], 409);
        }
    }




    public function approve(Request $request, Booking $booking)
    {
        if ($request->user()->id !== $booking->apartment->owner_id) {
            abort(403, 'You are not authorized to manage this booking.');
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'message' => 'This booking is not pending approval.'
            ], 409); // 409 Conflict
        }

        try {
            $this->bookingService->hasConflictingBookings(
                $booking->apartment_id,
                $booking->start_date,
                $booking->end_date,
                $booking->id
            );

            $booking->status = 'approved';
            $booking->save();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        Notification::send($booking->renter, new ApprovedNotification('Your booking has been approved.'));
        return response()->json([
            'message' => 'Booking approved successfully.',
            'booking' => $booking->fresh()
        ]);
    }

    public function reject(Request $request, Booking $booking)
    {
        if ($request->user()->id !== $booking->apartment->owner_id) {
            abort(403, 'You are not authorized to manage this booking.');
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'message' => 'This booking is not pending approval.'
            ], 409); // Conflict
        }

        $booking->status = 'rejected';
        $booking->save();

        Notification::send($booking->renter, new RejectNotification('Your booking has been rejected.'));

        return response()->json([
            'message' => 'Booking rejected successfully.',
            'booking' => $booking->fresh()
        ]);
    }


    public function show(Request $request, Booking $booking)
    {
        if ($request->user()->id !== $booking->renter_id && $request->user()->id !== $booking->apartment->owner_id) {
            abort(403, 'Unauthorized');
        }
        return response()->json($booking->load(['apartment', 'renter']));
    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    /**
     * إلغاء حجز (من قبل المستأجر أو المالك).
     */
    public function cancel(Request $request, Booking $booking)
    {
        $user = $request->user();


        $isRenter = $user->id === $booking->renter_id;
        $isOwner = $user->id === $booking->apartment->owner_id;

        if (! $isRenter && ! $isOwner) {
            abort(403, 'You are not authorized to cancel this booking.');
        }

        if (in_array($booking->status, ['completed', 'canceled'])) {
            return response()->json([
                'message' => 'This booking cannot be canceled as it is already considered final.'
            ], 409); // Conflict
        }

        $booking->status = 'canceled';
        $booking->save();

        return response()->json([
            'message' => 'Booking has been canceled successfully.',
            'booking' => $booking->fresh()
        ]);
    }


    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        if ($request->user()->id !== $booking->renter_id) {
            abort(403, 'You are not authorized to update this booking.');
        }

        if (in_array($booking->status, ['canceled', 'rejected', 'completed'])) {
            return response()->json(['message' => 'This booking cannot be updated because it is final.'], 409);
        }

        $validatedData = $request->validated();
        $newStartDate = $validatedData['start_date'] ?? $booking->start_date;
        $newEndDate = $validatedData['end_date'] ?? $booking->end_date;

        try {
            // في BookingController@update
            $this->bookingService->hasConflictingBookings(
                $booking->apartment_id,
                $newStartDate,
                $newEndDate,
                $booking->id // تجاهل الحجز الحالي
            );


            $this->bookingService->hasConflictingBookings(
                $booking->apartment_id,
                $newStartDate,
                $newEndDate,
                $booking->id
            );

            $startDate = Carbon::parse($newStartDate);
            $endDate = Carbon::parse($newEndDate);
            $numberOfNights = $startDate->diffInDays($endDate);
            $totalPrice = $numberOfNights * $booking->apartment->price;

            $booking->start_date = $newStartDate;
            $booking->end_date = $newEndDate;
            $booking->total_price = $totalPrice;
            $booking->status = 'pending';
            $booking->save();
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return response()->json([
            'message' => 'Booking update request sent successfully. Waiting for owner approval.',
            'booking' => $booking->fresh(),
        ]);
    }

    public function getOwnerBookings(Request $request)
    {
        $owner = $request->user();

        $ownerApartmentIds = $owner->apartments()->pluck('id');

        $pendingBookings = Booking::query()
            ->whereIn('apartment_id', $ownerApartmentIds)

            ->where('status', 'pending')

            ->with(['apartment:id,apartment_name', 'renter:id,first_name,last_name,profile_image_path'])

            ->oldest()

            ->get(); // نستخدم get() بدلاً من paginate() إذا كانت القائمة لن تكون ضخمة جداً

        // 3. إرجاع الرد
        return response()->json($pendingBookings);
    }
}
