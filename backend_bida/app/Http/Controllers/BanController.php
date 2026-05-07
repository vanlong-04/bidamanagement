<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ban;

class BanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    /**
     * Get the ban data.
     */
    public function getBan(Request $request)
    {
        $data = Ban::with('activeBooking')->get();
        return response()->json([
            'data' => $data,
        ]);
    }
}
