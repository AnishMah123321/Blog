<?php

namespace App\Http\Controllers\Api\Post;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Post;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class PostController extends Controller
{

    /**
     * @OA\Get(
     * path="/api/get-all-post",
     * operationId="GetAllPost",
     * tags={"Post"},
     * summary="Get All Post",
     * description="Get All Post",
     * security={{"bearer_token":{}}},
     *      @OA\Response(
     *          response=201,
     *          description="Fetch Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Fetch Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function getAllPost()
    {
        try {
            try {
                $posts = Post::orderBy('id', 'desc')->with('categories')->with('comments')->with('user')->get();
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => 'Error',
                    'message' => 'Technical Error',
                ]);
            }
            if ($posts) {
                return response()->json([
                    'status' =>  'Success',
                    'posts' => $posts,
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => 'Error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @OA\Get(
     * path="/api/get-total-post",
     * operationId="GetTotalPost",
     * tags={"Post"},
     * summary="Get Total Post",
     * description="Get Total Post",
     * security={{"bearer_token":{}}},
     *      @OA\Response(
     *          response=201,
     *          description="Fetch Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Fetch Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function getTotalPost()
    {
        try {
            try {
                $posts = Post::count();
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => 'Error',
                    'message' => 'Technical Error',
                ]);
            }
            if ($posts) {
                return response()->json([
                    'success' => true,
                    'posts' => $posts,
                ]);
            }else{
                return response()->json([
                    'success' => true,
                    'posts' => '0',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @OA\Post(
     * path="/api/save-post",
     * operationId="SavePost",
     * tags={"Post"},
     * summary="Save Post",
     * description="Save Post here",
     * security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"title","post","category_id"},
     *               @OA\Property(property="category_id", type="text"),
     *               @OA\Property(property="title", type="text"),
     *               @OA\Property(property="post", type="text")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Posted Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Posted Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'category_id' => 'required',
                'title' => 'required',
                'post' => 'required',
            ]);
            try {
                $data = [
                    'customer_id' => Auth::user()->id,
                    'category_id' => $validated['category_id'],
                    'title' => $validated['title'],
                    'post' => $validated['post'],
                ];
                $post = Post::create($data);

                //LOG
                $logData = array(
                    'user_id' => Auth::user()->id,
                    'action' => 'Store Post',
                    'request' => json_encode($request->all()),
                );
                $log = Log::create($logData);
                
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => 'Error',
                    'message' => 'Technical Error',
                ]);
            }
            $success['status'] =  'Success';
            $success['mssg'] =  'Successfully added post';
            DB::commit();
            return response()->json(['data' => $success]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => 'Error',
                'message' => $e->getMessage(),
            ]);
        }
    }


    /**
     * @OA\Post(
     * path="/api/update-post",
     * operationId="UpdatePost",
     * tags={"Post"},
     * summary="Update Post",
     * description="Update Post here",
     * security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id"},
     *               @OA\Property(property="id", type="text"),
     *               @OA\Property(property="category_id", type="text"),
     *               @OA\Property(property="title", type="text"),
     *               @OA\Property(property="post", type="text")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Updated Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Updated Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'id' => 'required',
            ]);
            try {
                $posts = Post::findOrFail($request->id);
                $result = $posts->update([
                    'category_id' => $request->category_id ? $request->category_id : $posts->category_id,
                    'title' => $request->title ? $request->title : $posts->title,
                    'post' => $request->post ? $request->post : $posts->post,
                ]);

                 //LOG
                 $logData = array(
                    'user_id' => Auth::user()->id,
                    'action' => 'Update Post',
                    'request' => json_encode($request->all()),
                );
                $log = Log::create($logData);
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => 'Error',
                    'message' => 'Technical Error',
                ]);
            }
            if ($result) {
                DB::commit();
                return response()->json([
                    'success' => "Success",
                    'message' => "Post Update Successfully",
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => "Error",
                    'message' => "Some Problem Has Occured",
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => "Error",
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/delete-post/{id}",
     * operationId="DeletePost",
     * tags={"Post"},
     * summary="Delete Post",
     * description="Delete Post",
     * security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          description="Post Id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Delete Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Delete Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            try {
                $posts = Post::findOrFail($id);
                $result = $posts->delete();
                 //LOG
                 $logData = array(
                    'user_id' => Auth::user()->id,
                    'action' => 'Delete Post',
                    'request' => json_encode($id),
                );
                $log = Log::create($logData);
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => 'Error',
                    'message' => 'Technical Error',
                ]);
            }
            if ($result) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Post Delete Successfully",
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Some Problem",
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => "Error",
                'message' => $e->getMessage(),
            ]);
        }
    }


    /**
     * @OA\Put(
     * path="/api/edit-post/{id}",
     * operationId="EditPost",
     * tags={"Post"},
     * summary="Edit Post",
     * description="Edit Post",
     * security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          description="Post Id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Edit Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Edit Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function edit($id)
    {
        try {
            DB::beginTransaction();
            try {
                $posts = Post::findOrFail($id);
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => 'Error',
                    'message' => 'Technical Error',
                ]);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'posts' => $posts
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
