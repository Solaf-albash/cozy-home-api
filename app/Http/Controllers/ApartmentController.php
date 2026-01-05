<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use App\Http\Resources\ApartmentDetailResource;
use App\Http\Resources\ApartmentResource;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApartmentController extends Controller
{

/**
     * عرض قائمة الشقق مع فلترة متقدمة.
     */
    public function index(Request $request)
    {
        $request->validate([
            'governorate' => 'sometimes|string',
            'city' => 'sometimes|string',
            'min_price' => 'sometimes|numeric|min:0',
            'max_price' => 'sometimes|numeric|gte:min_price', // يجب أن يكون السعر الأقصى أكبر من الأدنى
        ]);

        $apartmentsQuery = Apartment::query()
            ->where('status', 'available');
        // فلتر المحافظة (بحث دقيق)
        $apartmentsQuery->when($request->filled('governorate'), function ($query) use ($request) {
            $query->where('governorate', $request->query('governorate'));
        });
        // فلتر المدينة (بحث دقيق)
        $apartmentsQuery->when($request->filled('city'), function ($query) use ($request) {
            $query->where('city', $request->query('city'));
        });
        // فلتر نطاق السعر
        $apartmentsQuery->when($request->filled('min_price'), function ($query) use ($request) {
            $query->where('price', '>=', $request->query('min_price'));
        });

        $apartmentsQuery->when($request->filled('max_price'), function ($query) use ($request) {
            $query->where('price', '<=', $request->query('max_price'));
        });

        $apartments = $apartmentsQuery->latest()->paginate(10);

        return ApartmentResource::collection($apartments);
    }


public function store(StoreApartmentRequest $request)
{
    // 1. الحصول على البيانات التي تم التحقق منها
    $validatedData = $request->validated();

    // 2. معالجة الصور
    $imagePaths = [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $imageFile) {
            $path = $imageFile->store('apartments', 'public');
            $imagePaths[] = $path;
        }
    }

    // 3. دمج كل البيانات في مصفوفة واحدة
    $dataToCreate = array_merge(
        $validatedData,
        [
            'owner_id' => $request->user()->id,
            'images' => $imagePaths,
        ]
    );

    // 4. إنشاء الشقة
    $apartment = Apartment::create($dataToCreate);

    // 5. إرجاع الرد
    return response()->json([
        'message' => 'Apartment created successfully.',
        'apartment' => new ApartmentResource($apartment) // سنحتاج لتعديل هذا أيضاً
    ], 201);
}

/* عرض بيانات شقة محددة.
 */
public function show(Apartment $apartment)
{
    return new ApartmentDetailResource($apartment);
}



public function update(UpdateApartmentRequest $request, Apartment $apartment)
{
    $validatedData = $request->validated();

    $apartment->update($validatedData);

    return response()->json([
        'message' => 'Apartment updated successfully.',
        'apartment' => new ApartmentDetailResource($apartment->fresh())
    ]);
}


    // ... (destroy) ...
public function destroy(Request $request, Apartment $apartment)
{
    // 1. التحقق من الصلاحية
    if ($request->user()->id !== $apartment->owner_id) {
        abort(403, 'You are not authorized to delete this apartment.');
    }

    // 2. حذف الصور من الخادم (خطوة احترافية)
    // نتأكد أن $apartment->images هي مصفوفة وليست null
    if (is_array($apartment->images)) {
        Storage::disk('public')->delete($apartment->images);
    }

    $apartment->delete();

    return response()->json(['message' => 'Apartment deleted successfully.'], 200);
}
}
