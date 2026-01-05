<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateProfileRequest; // <-- 1. استدعاء الـ Request الجديد
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    public function update(UpdateProfileRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->user();
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image_path) {
                Storage::disk('public')->delete($user->profile_image_path);
            }
            $validatedData['profile_image_path'] = $request->file('profile_image')->store('profiles', 'public');
        }

        if ($request->hasFile('identity_image')) {
            if ($user->identity_image_path) {
                Storage::disk('public')->delete($user->identity_image_path);
            }
            $validatedData['identity_image_path'] = $request->file('identity_image')->store('identities', 'public');
        }

        $user->update($validatedData);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user->fresh(),
        ]);
    }
}
