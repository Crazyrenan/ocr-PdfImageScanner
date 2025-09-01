<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OcrController;

Route::get('/', function () {
    return view('welcome');
});

//iimage reading
Route::get('ocr', [OcrController::class, 'showUploadForm'])->name('ocr.form');
Route::post('ocr', [OcrController::class, 'processOcr'])->name('ocr.process');


//pdf serach
Route::get('/search', [OcrController::class, 'showSearchForm'])->name('search.form');
Route::get('/search/results', [OcrController::class, 'handleSearch'])->name('search.results');