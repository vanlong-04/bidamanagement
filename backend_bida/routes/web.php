<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Serve storage files - không cần /api prefix
Route::get('/image/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);
    if (!file_exists($file)) {
        return response()->json(['error' => 'File not found: ' . $file], 404);
    }
    return response()->file($file, [
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('image.serve');
