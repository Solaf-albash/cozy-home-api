<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Apartment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. البحث عن المالك الذي أنشأناه في UserSeeder
        $owner = User::where('role', 'owner')->first();

        // 2. التحقق من وجود المالك قبل المتابعة
        if ($owner) {

            // --- إنشاء الشقة الأولى ---
            Apartment::create([
                'owner_id'          => $owner->id,

                // --- استخدام الأسماء الجديدة ---
                'apartment_name'    => 'Cozy Studio in Mezzeh',
                'governorate'       => 'Damascus',
                'city'              => 'Mezzeh',
                'detailed_address'  => 'West Villas, Near Al-Jalaa Park, Building 5, 2nd Floor',
                'price'             => 250.00,
                'rent_type'         => 'daily',

                'description'       => 'A beautiful and quiet studio, perfect for short stays and business trips. Fully furnished with a small kitchen and a modern bathroom.',
                'specifications'    => json_encode(['rooms' => 1, 'wifi' => true, 'ac' => true, 'tv' => true]),
                'images'            => json_encode([
                    'seed/apartments/apartment1_1.jpg',
                    'seed/apartments/apartment1_2.jpg',
                ]),
                'status'            => 'available',
            ]);

            // --- إنشاء الشقة الثانية ---
            Apartment::create([
                'owner_id'          => $owner->id,
                'apartment_name'    => 'Spacious Flat with Sea View',
                'governorate'       => 'Latakia',
                'city'              => 'Latakia',
                'detailed_address'  => 'American University Street, Opposite to the main gate',
                'price'             => 12000.00,
                'rent_type'         => 'monthly',
                'description'       => 'A large 3-bedroom flat with a stunning sea view. Great for families and long-term stays.',
                'specifications'    => json_encode(['rooms' => 3, 'wifi' => true, 'ac' => false, 'balcony' => true]),
                'images'            => json_encode([
                    'seed/apartments/apartment2_1.jpg',
                ]),
                'status'            => 'available',
            ]);
        }
    }
}
