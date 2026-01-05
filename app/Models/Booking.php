<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{

    protected $fillable = [
        'renter_id',
        'apartment_id',
        'start_date',
        'end_date',
        'total_price',
        'status',
        'number_of_persons',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function review()
    {
        return $this->hasOne(Review::class);
    }
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
    public function renter()
    {
        return $this->belongsTo(User::class, 'renter_id');
    }
    /**
     * Scope a query to only include conflicting bookings (only approved).
     */
    public function scopeConflicting($query, int $apartmentId, string $startDate, string $endDate)
    {
        return $query->where('apartment_id', $apartmentId)
            ->where('status', 'approved') // 👇👇 نتحقق فقط من الحجوزات الموافق عليها
            ->where(function ($q) use ($startDate, $endDate) {
                // الشرط الصحيح 100% لتداخل الفترات الزمنية
                // Start A is before End B AND End A is after Start B
                $q->where('start_date', '<', $endDate)
                    ->where('end_date', '>', $startDate);
            });
    }
}
