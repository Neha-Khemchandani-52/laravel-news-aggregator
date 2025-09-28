<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\UserPreferenceController;


Route::get('/demo-token', function () {
    $user = User::firstOrCreate(
        ['email' => 'demotesting@example.com'],
        [
            'name' => 'Demo Testing User',
            'password' => bcrypt('password'),
        ]
    );

    $token = $user->createToken('DemoToken')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => $user,
    ]);
});

// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    //Route::post('/logout', [AuthController::class, 'logout']);

    // articles, authors, sources, categories proctected routes

    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{article}', [ArticleController::class, 'show']);
    Route::get('/authors', [AuthorController::class, 'index']);
    Route::get('/sources', [SourceController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);

    // user preferences
    Route::post('/user/preferences', [UserPreferenceController::class, 'store']);
    Route::get('/user/preferences', [UserPreferenceController::class, 'show']);
});
