<?php
namespace App\Console\Commands;
use App\Models\Booking;
use Illuminate\Console\Command;
use Carbon\Carbon;

class UpdateBookingStatuses extends Command
{
    /**
     * اسم وتوقيع الأمر في الكونسول.
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:update-status';

    /**
     * توصيف الأمر.
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates booking statuses from approved to completed for past bookings.';

    /**
     * تنفيذ منطق الأمر.
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update booking statuses...');

        // تاريخ "الأمس"
        $yesterday = Carbon::yesterday()->toDateString();

        // 1. البحث عن الحجوزات المستهدفة
        $bookingsToComplete = Booking::where('status', 'approved')
            ->where('end_date', '<=', $yesterday)
            ->get();

        if ($bookingsToComplete->isEmpty()) {
            $this->info('No bookings to update.');
            return 0; // إنهاء الأمر بنجاح
        }

        $count = 0;
        foreach ($bookingsToComplete as $booking) {
            // 2. تحديث حالة كل حجز
            $booking->status = 'completed';
            $booking->save();
            $count++;
        }

        $this->info("Successfully updated {$count} booking(s) to 'completed'.");
        return 0; // إنهاء الأمر بنجاح
    }
}
