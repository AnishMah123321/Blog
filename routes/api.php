<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Comment\CommentController;
use App\Http\Controllers\Api\Post\PostController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Public

//Post
Route::get('/get-all-post', [PostController::class, 'getAllPost']);
Route::get('/get-total-post', [PostController::class, 'getTotalPost']);

//Comment
Route::get('/get-all-comment', [CommentController::class, 'getAllComment']);
Route::get('/get-total-comment/{id}', [CommentController::class, 'getTotalComment']);

//Subscribe
Route::get('/get-subscribers/{id}', [UserController::class, 'getSubscriber']);
Route::get('/get-subscribed/{id}', [UserController::class, 'getSubscribed']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    //Post
    Route::post('/save-post', [PostController::class, 'store']);
    Route::put('/edit-post/{id}', [PostController::class, 'edit']);
    Route::post('/update-post', [PostController::class, 'update']);
    Route::delete('/delete-post/{id}', [PostController::class, 'delete']);
    Route::post('/upvote/{id}', [PostController::class, 'upVote']);
    Route::post('/downvote/{id}', [PostController::class, 'downVote']);


    //Comment
    Route::post('/save-comment', [CommentController::class, 'store']);
    Route::put('/edit-comment/{id}', [CommentController::class, 'edit']);
    Route::post('/update-comment', [CommentController::class, 'update']);
    Route::delete('/delete-comment/{id}', [CommentController::class, 'delete']);

    //Subscribe
    Route::post('/subscribe', [UserController::class, 'subscribe']);
    Route::post('/unsubscribe', [UserController::class, 'subscribe']);

});