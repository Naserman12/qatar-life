<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    // عرض جميع الأحياء
    public function index()
    {
        return District::latest()->get();
    }

    // إضافة حي جديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $district = District::create([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'تم إضافة الحي بنجاح',
            'district' => $district
        ]);
    }

    // حذف حي
    public function destroy($id)
    {
        $district = District::findOrFail($id);
        $district->delete();

        return response()->json([
            'message' => 'تم حذف الحي'
        ]);
    }
}