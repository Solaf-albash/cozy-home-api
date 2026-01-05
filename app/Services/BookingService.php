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
/**
     */
    public function checkForConflicts(
        int $apartmentId,
        string $startDate,
        string $endDate,
        ?int $exceptBookingId = null,
        array $statuses = ['approved'] // <-- الإضافة الجديدة
    ): void {
        $query = Booking::where('apartment_id', $apartmentId)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('start_date', '<', $endDate)
                    ->where('end_date', '>', $startDate);
            })
            ->whereIn('status', $statuses) // <-- استخدام مصفوفة الحالات
            ->lockForUpdate();

        if ($exceptBookingId) {
            $query->where('id', '!=', $exceptBookingId);
        }

        if ($query->exists()) {
            throw new Exception('Sorry, the selected dates conflict with another booking.');
        }
    } }
