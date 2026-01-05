<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phonenumber',
        'password',
        'birth_date',
        'profile_image_path',
        'id_image_path',
        'role',
        'status',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $appends = [
    'photo_url',
    'id_img_url',

];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    //الشقق التي يملكها هذا المستخدم (إذا كان owner)
    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'owner_id');
    }
    //الحجوزات التي قام بها المستخدم اذا كان renter
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'renter_id');
    }

    // الشقق المفضلة لهذا المستخدم

    public function favoriteApartments()
    {
        // المستخدم يملك العديد من الشقق المفضلة
        // هنا نخبره باسم الجدول الوسيط صراحة
        return $this->belongsToMany(Apartment::class, 'favorities', 'user_id', 'apartment_id')
            ->withTimestamps();
    }
    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $imagePath = $this->profile_image_path;

                if ($imagePath) {
                    return asset(Storage::url($imagePath));
                }

                return null;
            },
        );
    }
    protected function idImgUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $imagePath = $this->id_image_path;
                if ($imagePath) {
                    return asset(Storage::url($imagePath));
                }

                return null;
            },
        );
    }
}
