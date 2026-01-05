<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'apartment_name' => $this->apartment_name,
            'governorate' => $this->governorate,
            'city' => $this->city,
            'detailed_address' => $this->detailed_address,
            'price' => $this->price,
            'rent_type' => $this->rent_type,
            'description' => $this->description,
            'specifications' => $this->specifications,
            'images' => $this->image_urls, // مصفوفة الروابط الكاملة
            'status' => $this->status,
            'owner' => [
                'id' => $this->owner->id,
                'full_name' => $this->owner->first_name . ' ' . $this->owner->last_name,
                'profile_image' => $this->owner->photo_url,
            ]
        ];
    }
}
