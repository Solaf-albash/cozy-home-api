<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function approveUser(User $user)
    {
        if ($user->status !== 'pending') {
            return response()->json(['message' => 'This user account is not pending approval.'], 409); // 409 Conflict
        }

        $user->status = 'approved';
        $user->save();

        return response()->json([
            'message' => 'User account approved successfully.',
            'user' => $user->only([
                'id',
                'first_name',
                'last_name',
                'phonenumber',
                'role',
                'birth_date',
                'status',
                'photo_url',
                'id_img_url',
            ]),
        ], 200);
    }

    public function rejectUser(User $user)
    {
        // 1. التحقق من أن الحساب 'pending' أصلاً
        if ($user->status !== 'pending') {
            return response()->json([
                'message' => 'This user account is not pending a decision.'
            ], 409); // 409 Conflict
        }

        // 2. تغيير الحالة إلى 'blocked' وحفظها
        $user->status = 'blocked';
        $user->save();

        // (اختياري) يمكننا إرسال إشعار للمستخدم هنا نخبره بأن طلبه قد تم رفضه.

        // 3. إرجاع رد ناجح
        return response()->json([
            'message' => 'User account has been rejected and blocked.',
            'user' => $user->only([
                'id',
                'first_name',
                'last_name',
                'phonenumber',
                'role',
                'birth_date',
                'status',
                'photo_url',
                'id_img_url',
            ]),
        ]);
    }
    public function loginAsDefaultAdmin()
    {
        $admin = User::where('phonenumber', '0911111111')
            ->where('role', 'admin')
            ->first();

        if (!$admin) {
            return response()->json(['message' => 'Default admin account not found. Please run database seeder.'], 404);
        }
        $admin->tokens()->delete();
        $token = $admin->createToken('admin_auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Default admin logged in successfully.',
            'user' => $admin->only([
                'first_name',
                'last_name',
                'phonenumber',
                'role',
                'birth_date',
                'status',
                'photo_url',
                'id_img_url',
            ])  ,
            'access_token' => $token,
        ]);
    }
    public function getPendingUsers()
    {
        $pendingUsers = User::where('status', 'pending')
            ->oldest()
            ->get();
        return response()->json($pendingUsers->map(function ($user) {
            return $user->only([
                'id',
                'first_name',
                'last_name',
                'phonenumber',
                'role',
                'birth_date',
                'status',
                'photo_url',
                'id_img_url',
            ]);
        }));
    }

}
