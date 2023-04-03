<?php

namespace App\Http\Controllers\Api\Comment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Log;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class CommentController extends Controller
{

    /**
     * @OA\Get(
     * path="/api/get-all-comment",
     * operationId="GetAllComment",
     * tags={"Comment"},
     * summary="Get All Comment",
     * description="Get All Comment",
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
    public function getAllComment()
    {
        try {
            try{
            $comments = Comment::orderBy('id', 'desc')->with('user')->get();
            } catch (Exception $e){
                DB::rollBack();
                return response()->json([
                    'success' => 'Error',
                    'message' => 'Technical Error',
                ]);
            }
            if ($comments) {
                return response()->json([
                    'status' =>  'Success',
                    'comments' => $comments,
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
     * path="/api/get-total-comment/{id}",
     * operationId="GetTotalComment",
     * tags={"Comment"},
     * summary="Get Total Comment",
     * description="Get Total Comment",
     * security={{"bearer_token":{}}},
     *    @OA\Parameter(
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
    public function getTotalComment($id)
    {
        try {
            try{
            $comments = Comment::where('post_id',$id)->count();
            } catch (Exception $e){
                DB::rollBack();
                return response()->json([
                    'success' => 'Error',
                    'message' => 'Technical Error',
                ]);
            }
            if ($comments) {
                return response()->json([
                    'success' => true,
                    'comments' => $comments,
                ]);
            }else{
                return response()->json([
                    'success' => true,
                    'comments' => '0',
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
     * path="/api/save-comment",
     * operationId="SaveComment",
     * tags={"Comment"},
     * summary="Save Comment",
     * description="Save Comment here",
     * security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"comment","post_id"},
     *               @OA\Property(property="post_id", type="text"),
     *               @OA\Property(property="comment", type="text")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Commented Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Commented Successfully",
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
                'post_id' => 'required',
                'comment' => 'required',
            ]);
            $data = [
                'customer_id' => Auth::user()->id,
                'post_id' => $validated['post_id'],
                'comment' => $validated['comment'],
            ];
            try{
            $comment = Comment::create($data);

             //LOG
             $logData = array(
                'user_id' => Auth::user()->id,
                'action' => 'Store Comment',
                'request' => json_encode($request->all()),
            );
            $log = Log::create($logData);

            } catch (Exception $e){
                DB::rollBack();
                return response()->json([
                    'success' => 'Error',
                    'message' => 'Technical Error',
                ]);
            }
            $success['status'] =  'Success';
            $success['mssg'] =  'Successfully added comment';
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
     * path="/api/update-comment",
     * operationId="UpdateComment",
     * tags={"Comment"},
     * summary="Update Comment",
     * description="Update Comment here",
     * security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"id"},
     *               @OA\Property(property="id", type="text"),
     *               @OA\Property(property="comment", type="text")
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
                'comment' => 'required',
            ]);
            try{
                $comments = Comment::findOrFail($request->id);
            $result = $comments->update([
                'comment' => $request->comment ? $request->comment : $comments->comment,
            ]);

             //LOG
             $logData = array(
                'user_id' => Auth::user()->id,
                'action' => 'Update Comment',
                'request' => json_encode($request->all()),
            );
            $log = Log::create($logData);

            } catch (Exception $e){
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
                    'message' => "Comment Update Successfully",
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
     * path="/api/delete-comment/{id}",
     * operationId="DeleteComment",
     * tags={"Comment"},
     * summary="Delete Comment",
     * description="Delete Comment",
     * security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          description="Comment Id",
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
            try{
                $comments = Comment::findOrFail($id);
                $result = $comments->delete();

                 //LOG
                 $logData = array(
                    'user_id' => Auth::user()->id,
                    'action' => 'Delete Comment',
                    'request' => json_encode($id),
                );
                $log = Log::create($logData);

            } catch (Exception $e){
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
                    'message' => "Comment Delete Successfully",
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
     * path="/api/edit-comment/{id}",
     * operationId="EditComment",
     * tags={"Comment"},
     * summary="Edit Comment",
     * description="Edit Comment",
     * security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          description="Comment Id",
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
            try{
            $comments = Comment::findOrFail($id);
            } catch (Exception $e){
                DB::rollBack();
                return response()->json([
                    'success' => 'Error',
                    'message' => 'Technical Error',
                ]);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'comments' => $comments
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
