<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Send notification to a single device
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function sendToDevice(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array'
        ]);

        $result = $this->firebaseService->sendToDevice(
            $request->token,
            $request->title,
            $request->body,
            $request->data ?? []
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Send notification to multiple devices
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function sendToMultipleDevices(Request $request): JsonResponse
    {
        $request->validate([
            'tokens' => 'required|array',
            'tokens.*' => 'required|string',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array'
        ]);

        $result = $this->firebaseService->sendToMultipleDevices(
            $request->tokens,
            $request->title,
            $request->body,
            $request->data ?? []
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Send notification to a topic
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function sendToTopic(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'required|string',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array'
        ]);

        $result = $this->firebaseService->sendToTopic(
            $request->topic,
            $request->title,
            $request->body,
            $request->data ?? []
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Send notification to a user by user ID
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function sendToUser(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array'
        ]);

        $user = User::find($request->user_id);

        if (!$user || !$user->fcm_token) {
            return response()->json([
                'success' => false,
                'message' => 'User does not have an FCM token'
            ], 404);
        }

        $result = $this->firebaseService->sendToDevice(
            $user->fcm_token,
            $request->title,
            $request->body,
            $request->data ?? []
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Send notification to all users
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function sendToAllUsers(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array'
        ]);

        $tokens = User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

        if (empty($tokens)) {
            return response()->json([
                'success' => false,
                'message' => 'No users with FCM tokens found'
            ], 404);
        }

        $result = $this->firebaseService->sendToMultipleDevices(
            $tokens,
            $request->title,
            $request->body,
            $request->data ?? []
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Subscribe user to a topic
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function subscribeToTopic(Request $request): JsonResponse
    {
        $request->validate([
            'tokens' => 'required|array',
            'tokens.*' => 'required|string',
            'topic' => 'required|string'
        ]);

        $result = $this->firebaseService->subscribeToTopic(
            $request->tokens,
            $request->topic
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Unsubscribe user from a topic
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function unsubscribeFromTopic(Request $request): JsonResponse
    {
        $request->validate([
            'tokens' => 'required|array',
            'tokens.*' => 'required|string',
            'topic' => 'required|string'
        ]);

        $result = $this->firebaseService->unsubscribeFromTopic(
            $request->tokens,
            $request->topic
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Save or update FCM token for authenticated user
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function saveFcmToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $user->fcm_token = $request->token;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'FCM token saved successfully'
        ]);
    }

    /**
     * Generate a test FCM token for development/testing
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function generateTestToken(Request $request): JsonResponse
    {
        // دروستکردنی token-ێکی تاقیکردنەوە
        $testToken = 'test_fcm_token_' . uniqid() . '_' . bin2hex(random_bytes(16));

        $userData = null;

        // ئەگەر user_id نێردرابێت، token-ەکە هەڵبگرە بۆ ئەو بەکارهێنەرە
        if ($request->has('user_id')) {
            $user = User::find($request->user_id);
            if ($user) {
                $user->fcm_token = $testToken;
                $user->save();
                
                $userData = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found with ID: ' . $request->user_id
                ], 404);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Test FCM token generated successfully',
            'token' => $testToken,
            'user' => $userData,
            'saved_to_database' => $userData !== null,
            'note' => $userData ? 'Token saved to database for user' : 'Token generated but not saved. Provide user_id to save.'
        ]);
    }

    /**
     * Get all users with their FCM tokens
     * 
     * @return JsonResponse
     */
    public function getUsersWithTokens(): JsonResponse
    {
        $users = User::select('id', 'name', 'email', 'fcm_token')
            ->whereNotNull('fcm_token')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $users->count(),
            'users' => $users
        ]);
    }

    /**
     * Manually set FCM token for a user
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function setUserToken(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'token' => 'required|string'
        ]);

        // ئەگەر user_id نەبێت یان user نەدۆزرایەوە، یەکەمیان وەربگرە یان دروستی بکە
        if ($request->user_id) {
            $user = User::find($request->user_id);
        } else {
            $user = User::first();
        }

        // ئەگەر هیچ بەکارهێنەرێک نەبوو، یەکێک دروست بکە
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test' . time() . '@example.com',
                'password' => bcrypt('password')
            ]);
        }

        $user->fcm_token = $request->token;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'FCM token set successfully for user',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'token' => $user->fcm_token
            ]
        ]);
    }
}
