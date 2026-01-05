<?php

namespace App\Services;

use App\Models\Booking;
use Exception;

class BookingService
{
    /**
     * Check for conflicting bookings for a given apartment and date range.
     *
     * @param int $apartmentId
     * @param string $startDate
     * @param string $endDate
     * @return bool
     * @throws Exception
     */
    public function hasConflictingBookings(int $apartmentId, string $startDate, string $endDate): bool
    {
        try {
            $conflictCount = Booking::where('apartment_id', $apartmentId)
                ->where('status', 'approved')
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })
                ->count();
        } catch (\Exception $e) {
            throw new Exception("Error checking for conflicting bookings: " . $e->getMessage());
        }

        return $conflictCount > 0;
    }
}
