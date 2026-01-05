<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request; // <--- تأكدي 100% من وجود هذا السطر


class AuthController extends Controller
{
public function register(RegisterRequest $request)
{

    $validatedData = $request->validated();

    //  معالجة وتقسيم الاسم
    $nameParts = explode(' ', $validatedData['fullname'], 2);
    $firstName = $nameParts[0];
    $lastName = $nameParts[1] ?? '';

    //  معالجة ورفع الصور
    $profileImagePath = $request->file('profile_image')->store('profiles', 'public');
    $identityImagePath = $request->file('id_image')->store('identities', 'public');

    // 4. إنشاء المستخدم في قاعدة البيانات
    $user = User::create([
        // --- البيانات النصية ---
        'first_name' => $firstName,
        'last_name' => $lastName,
        'phonenumber' => $validatedData['phonenumber'],
        'password' => Hash::make($validatedData['password']),
        'role' => $validatedData['role'],
        'birth_date' => $validatedData['birth_date'],

        // --- مسارات الصور ---
        'profile_image_path' => $profileImagePath,
        'id_image_path' => $identityImagePath,
    ]);

    // 5. إرجاع الرد
    return response()->json([
        'message' => 'User registration request sent successfully. Your account is pending approval.',
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
        ])
    ], 201);
}


    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('phonenumber', 'password'))) {
            return response()->json(['message' => 'Invalid mobile number or password'], 401);
        }

        $user = User::where('phonenumber', $request->phonenumber)->first();

        if ($user->status !== 'approved') {
            Auth::logout();
            return response()->json(['message' => 'Your account is not active'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->only([
                'first_name',
                'last_name',
                'phonenumber',
                'role',
                'birth_date',
                'status',
                'photo_url',
                'id_img_url',
            ]),
            'access_token' => $token,
        ], 200);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}
