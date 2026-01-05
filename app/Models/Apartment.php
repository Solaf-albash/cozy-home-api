<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Apartment extends Model
{

    protected $fillable = [
        'owner_id',
        'apartment_name',
        'governorate',
        'city',
        'detailed_address',
        'price',
        'rent_type',
        'description',
        'specifications',
        'images',
        'status',
    ];
    protected $appends = [
        'image_urls'
    ];
    protected $casts = [
        'specifications' => 'array',
        'images' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    /**
     * المستخدمون الذين أضافوا هذه الشقة إلى المفضلة
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorities', 'apartment_id', 'user_id');
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Booking::class);
    }
    protected function imageUrls(): Attribute
    {
        return Attribute::make(
            get: function () {
                $imagePaths = $this->images ?? [];

                // If images were stored as a JSON string (or otherwise returned as string), decode safely
                if (is_string($imagePaths)) {
                    $decoded = json_decode($imagePaths, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $imagePaths = $decoded;
                    } else {
                        $imagePaths = [];
                    }
                }

                // Ensure we have an array or traversable before foreach
                if (!is_array($imagePaths) && !($imagePaths instanceof \Traversable)) {
                    $imagePaths = [];
                }

                $imageUrls = [];

                foreach ($imagePaths as $path) {
                    if ($path) {
                        $imageUrls[] = asset(Storage::url($path));
                    }
                }

                return $imageUrls;
            }
        );
    }
}
