<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Exception;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = storage_path('app/firebase/notification-send-c5364-firebase-adminsdk-fbsvc-5262b25ec2.json');
            
            if (!file_exists($credentialsPath)) {
                throw new Exception("Firebase credentials file not found");
            }
            
            $factory = (new Factory)
                ->withServiceAccount($credentialsPath);
            
            if (config('firebase.project_id')) {
                $factory = $factory->withProjectId(config('firebase.project_id'));
            }

            $this->messaging = $factory->createMessaging();
        } catch (Exception $e) {
            throw new Exception("Firebase initialization failed: " . $e->getMessage());
        }
    }

    /**
     * Send notification to a single device
     *
     * @param string $token
     * @param string $title
     * @param string $body
     * @param array $data
     * @return array
     */
    public function sendToDevice(string $token, string $title, string $body, array $data = []): array
    {
        try {
            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification);

            if (!empty($data)) {
                $message = $message->withData($data);
            }

            $result = $this->messaging->send($message);

            return [
                'success' => true,
                'message' => 'Notification sent successfully',
                'result' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to multiple devices
     *
     * @param array $tokens
     * @param string $title
     * @param string $body
     * @param array $data
     * @return array
     */
    public function sendToMultipleDevices(array $tokens, string $title, string $body, array $data = []): array
    {
        try {
            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::new()
                ->withNotification($notification);

            if (!empty($data)) {
                $message = $message->withData($data);
            }

            $result = $this->messaging->sendMulticast($message, $tokens);

            return [
                'success' => true,
                'message' => 'Notifications sent',
                'successful' => $result->successes()->count(),
                'failed' => $result->failures()->count(),
                'result' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send notifications: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to a topic
     *
     * @param string $topic
     * @param string $title
     * @param string $body
     * @param array $data
     * @return array
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): array
    {
        try {
            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification($notification);

            if (!empty($data)) {
                $message = $message->withData($data);
            }

            $result = $this->messaging->send($message);

            return [
                'success' => true,
                'message' => 'Notification sent to topic successfully',
                'result' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send notification to topic: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Subscribe tokens to a topic
     *
     * @param array $tokens
     * @param string $topic
     * @return array
     */
    public function subscribeToTopic(array $tokens, string $topic): array
    {
        try {
            $result = $this->messaging->subscribeToTopic($topic, $tokens);

            return [
                'success' => true,
                'message' => 'Subscribed to topic successfully',
                'result' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to subscribe to topic: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Unsubscribe tokens from a topic
     *
     * @param array $tokens
     * @param string $topic
     * @return array
     */
    public function unsubscribeFromTopic(array $tokens, string $topic): array
    {
        try {
            $result = $this->messaging->unsubscribeFromTopic($topic, $tokens);

            return [
                'success' => true,
                'message' => 'Unsubscribed from topic successfully',
                'result' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to unsubscribe from topic: ' . $e->getMessage()
            ];
        }
    }
}
