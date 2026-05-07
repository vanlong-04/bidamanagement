<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DichVu;
use Illuminate\Support\Facades\Storage;

class DichVuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function getDichVu(Request $request){
        $data = DichVu::all();
        return response()->json([
            'data' => $data,
        ]);
    }
    public function createDichVu(Request $request){
        $validated = $request->validate([
            'dich_vu_name' => ['required', 'string', 'max:100'],
            'loai_dich_vu' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('dichvu', 'public');
        }

        DichVu::create([
            'dich_vu_name' => $validated['dich_vu_name'],
            'loai_dich_vu' => $validated['loai_dich_vu'],
            'price' => $validated['price'],
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
        ]);
        return response()->json([
            'status' => 1,
            'message' => 'Tạo dịch vụ thành công!',
        ]);
    }
    public function updateDichVu(Request $request){
        $validated = $request->validate([
            'dich_vu_id' => ['required'],
            'dich_vu_name' => ['required', 'string', 'max:100'],
            'loai_dich_vu' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ]);

        $dichVu = DichVu::where('dich_vu_id', $validated['dich_vu_id'])->first();
        if (!$dichVu) {
            return response()->json([
                'status' => 0,
                'message' => 'Không tìm thấy dịch vụ!',
            ], 404);
        }

        $updatePayload = [
            'dich_vu_name' => $validated['dich_vu_name'],
            'loai_dich_vu' => $validated['loai_dich_vu'],
            'price' => $validated['price'],
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('dichvu', 'public');
            $updatePayload['image_path'] = $newPath;

            if ($dichVu->image_path) {
                Storage::disk('public')->delete($dichVu->image_path);
            }
        }

        $dichVu->update($updatePayload);
        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật dịch vụ thành công!',
        ]);
    }
    public function deleteDichVu(Request $request){
        $request->validate([
            'dich_vu_id' => ['required'],
        ]);

        $dichVu = DichVu::where('dich_vu_id', $request->dich_vu_id)->first();
        if (!$dichVu) {
            return response()->json([
                'status' => 0,
                'message' => 'Không tìm thấy dịch vụ!',
            ], 404);
        }

        if ($dichVu->image_path) {
            Storage::disk('public')->delete($dichVu->image_path);
        }

        $dichVu->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Xóa dịch vụ thành công!',
        ]);
    }

}
