<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tsung\NovaLabelCreator\Http\Controllers\PrintLabelController;
use Illuminate\Routing\Middleware\ValidateSignature;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('/fields', [PrintLabelController::class, "fields"]);
Route::post('/labels', [PrintLabelController::class, "labels"]);

Route::get('/download', [PrintLabelController::class, 'download'])
    ->name('label-creator.download')
    ->middleware(ValidateSignature::class);
