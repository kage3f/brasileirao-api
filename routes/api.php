<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrasileiraoController;
use App\Http\Controllers\BrasileiraoRodadasController;
use App\Http\Controllers\BrasileiraoNewsController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/brasileirao/classificacao', [BrasileiraoController::class, 'getClassificacao']);

Route::get('/brasileirao/rodada', [BrasileiraoRodadasController::class, 'getRodada']);

Route::get('/brasileirao/news', [BrasileiraoNewsController::class, 'getNews']);
