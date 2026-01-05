<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    /**
     * اسم الجدول في قاعدة البيانات.
     * The table associated with the model.
     *
     * @var string
     */
    // 👇👇 هذا هو السطر الأهم الذي يحل المشكلة
    protected $table = 'favorities';

    // الحقول التي يمكن تعبئتها
    protected $fillable = ['user_id', 'apartment_id'];
}
