<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApartmentResource;
use App\Models\Apartment;
use Illuminate\Http\Request;

class FavorityController extends Controller
{

    public function index(Request $request)
    {
        $favoriteApartments = $request->user()->favoriteApartments()->latest()->paginate(10);

        return ApartmentResource::collection($favoriteApartments);
    }


    public function toggle(Request $request, Apartment $apartment)
    {
        $user = $request->user();


        $result = $user->favoriteApartments()->toggle($apartment->id);

        if (count($result['attached']) > 0) {
            return response()->json([
                'message' => 'Apartment added to favorites successfully.',
                'is_favorite' => true
            ]);
        }

        return response()->json([
            'message' => 'Apartment removed from favorites successfully.',
            'is_favorite' => false
        ]);
    }
}
