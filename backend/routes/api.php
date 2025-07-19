<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\MenuLinkController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ContentBlockController;
use Illuminate\Http\Response;
use App\Http\Controllers\Api\UploadController;

Route::apiResource('teams', TeamController::class);
Route::apiResource('events', EventController::class);
Route::apiResource('members', MemberController::class);
Route::apiResource('news', NewsController::class);
Route::apiResource('menu-links', MenuLinkController::class);
Route::apiResource('pages', PageController::class);
Route::apiResource('content-blocks', ContentBlockController::class)->only(['store', 'update', 'destroy']);
Route::get('pages/slug/{slug}', [PageController::class, 'showBySlug']);
Route::post('upload-image', [UploadController::class, 'uploadImage']);
Route::post('delete-image', [UploadController::class, 'deleteImage']);
Route::get('cms/members', [\App\Http\Controllers\Api\MemberController::class, 'cmsList']);
