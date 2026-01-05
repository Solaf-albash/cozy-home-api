<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ApartmentResource extends JsonResource
{
    /**
     * تحويل المورد إلى مصفوفة.
     */
    public function toArray(Request $request): array
    {
        $averageRating = $this->reviews()->avg('rating');


        return [
            'id' => $this->id,
            'apartment_name' => $this->apartment_name,
            'governorate' => $this->governorate,
            'city' => $this->city,
            'price' => $this->price,
            'rent_type' => $this->rent_type,
            'thumbnail' => $this->image_urls[0] ?? null, // أول رابط كامل
            'average_rating' => $averageRating ? round($averageRating, 1) : null,
        ];
    }
}
