<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Subscriber;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;
class UserController extends Controller
{

    /**
     * @OA\Get(
     * path="/api/get-subscribers/{id}",
     * operationId="getSubscribers",
     * tags={"User"},
     * summary="Get All Subscribers",
     * description="Get All Subscribers",
     * security={{"bearer_token":{}}},
     *    @OA\Parameter(
     *          name="id",
     *          description="User Id",
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
    public function getSubscriber($id)
    {
        try {
            try {
                $subscribers = Subscriber::where('customer_id', $id)->orderBy('id', 'desc')->with('subscriberDetail')->get();
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => 'Error',
                    'message' => 'Technical Error',
                ]);
            }
            if ($subscribers) {
                return response()->json([
                    'status' =>  'Success',
                    'count' => count($subscribers),
                    'subscribers' => $subscribers,
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
     * path="/api/get-subscribed/{id}",
     * operationId="getSubscribed",
     * tags={"User"},
     * summary="Get All Subscribed",
     * description="Get All Subscribed",
     * security={{"bearer_token":{}}},
     *    @OA\Parameter(
     *          name="id",
     *          description="User Id",
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
    public function getSubscribed($id)
    {
        try {
            try {
                $subscribed = Subscriber::where('subscriber_user_id', $id)->orderBy('id', 'desc')->with('subscribedDetail')->get();
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => 'Error',
                    'message' => 'Technical Error',
                ]);
            }
            if ($subscribed) {
                return response()->json([
                    'status' =>  'Success',
                    'count' => count($subscribed),
                    'subscribed' => $subscribed,
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
     * @OA\Post(
     * path="/api/subscribe",
     * operationId="Subscribe",
     * tags={"User"},
     * summary="Subscribe",
     * description="Subscribe User",
     * security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"user_id"},
     *               @OA\Property(property="user_id", type="text"),
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
     * 
     *  @OA\Post(
     * * path="/api/unsubscribe",
     * operationId="Unsubscribe",
     * tags={"User"},
     * summary="Unsubscribe",
     * description="Unsubscribe User",
     * security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"user_id"},
     *               @OA\Property(property="user_id", type="text"),
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
    public function subscribe(Request $request)
    {
        try {
            $segment = request()->segment(2);
            DB::beginTransaction();

            $validated = $request->validate([
                'user_id' => 'required',
            ]);
            try {
                $subscribes = Subscriber::where('customer_id', $request->user_id)->where('subscriber_user_id', Auth::user()->id)->count();
                if ($subscribes > 0 && $segment == "unsubscribe") {

                    $unsubscribe = Subscriber::where('customer_id', $request->user_id)->where('subscriber_user_id', Auth::user()->id)->forcedelete();
                    //LOG
                    $logData = array(
                        'user_id' => Auth::user()->id,
                        'action' => 'Subscribe',
                        'request' => json_encode($request->user_id),
                    );
                    $log = Log::create($logData);
                } else if ($subscribes == 0 && $segment == "subscribe") {
                
                    $subscriberData = array(
                        'customer_id' => $request->user_id,
                        'subscriber_user_id' => Auth::user()->id,
                    );
                    $subscribe = Subscriber::create($subscriberData);

                    //LOG
                    $logData = array(
                        'user_id' => Auth::user()->id,
                        'action' => 'Unubscribe',
                        'request' => json_encode($request->user_id),
                    );
                    $log = Log::create($logData);
                }
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => 'Error',
                    'message' => 'Technical Error',
                ]);
            }
            if (isset($subscribe) && $subscribe) {
                DB::commit();
                return response()->json([
                    'success' => "Success",
                    'message' => "Subscribed",
                ]);
            } else if (isset($unsubscribe) && $unsubscribe) {
                DB::commit();
                return response()->json([
                    'success' => "Success",
                    'message' => "Unsubscribed",
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => "Error",
                    'message' => "Already " . ucwords($segment). "d",
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
}
