Firebase Cloud Messaging (FCM) Integration
دامەزراندنی سەرەتایی
1. پێویستیەکانی Firebase Console
پێش بەکارهێنان، پێویستە ئەم هەنگاوانە لە Firebase Console جێبەجێ بکەیت:

بچۆ بۆ Firebase Console
پرۆژەیەکی نوێ دروست بکە یان پرۆژەیەکی هەبوو هەڵبژێرە
بچۆ بۆ Project Settings > Service Accounts
کلیک بکە لەسەر Generate New Private Key
فایلی JSON دادەبەزێت
فایلی JSON بگوازەرەوە بۆ: storage/app/firebase/service-account.json
2. کۆنفیگکردنی .env
فایلی .env نوێ بکەرەوە:

FIREBASE_CREDENTIALS=storage/app/firebase/service-account.json
FIREBASE_PROJECT_ID=your-firebase-project-id
API Endpoints
Base URL
http://localhost:8000/api/notifications
1. ناردنی نۆتیفیکەیشن بۆ ئامێرێکی تایبەت
Endpoint: POST /send-to-device

Request Body:

{
    "token": "FCM_DEVICE_TOKEN",
    "title": "سەردێڕی نۆتیفیکەیشن",
    "body": "ناوەڕۆکی نۆتیفیکەیشن",
    "data": {
        "key": "value"
    }
}
2. ناردنی نۆتیفیکەیشن بۆ چەند ئامێرێک
Endpoint: POST /send-to-multiple

Request Body:

{
    "tokens": [
        "FCM_TOKEN_1",
        "FCM_TOKEN_2"
    ],
    "title": "سەردێڕی نۆتیفیکەیشن",
    "body": "ناوەڕۆکی نۆتیفیکەیشن",
    "data": {
        "key": "value"
    }
}
3. ناردنی نۆتیفیکەیشن بۆ بابەتێک (Topic)
Endpoint: POST /send-to-topic

Request Body:

{
    "topic": "news",
    "title": "سەردێڕی نۆتیفیکەیشن",
    "body": "ناوەڕۆکی نۆتیفیکەیشن",
    "data": {
        "key": "value"
    }
}
4. ناردنی نۆتیفیکەیشن بۆ بەکارهێنەرێکی دیاریکراو
Endpoint: POST /send-to-user

Request Body:

{
    "user_id": 1,
    "title": "سەردێڕی نۆتیفیکەیشن",
    "body": "ناوەڕۆکی نۆتیفیکەیشن",
    "data": {
        "key": "value"
    }
}
5. ناردنی نۆتیفیکەیشن بۆ هەموو بەکارهێنەران
Endpoint: POST /send-to-all

Request Body:

{
    "title": "سەردێڕی نۆتیفیکەیشن",
    "body": "ناوەڕۆکی نۆتیفیکەیشن",
    "data": {
        "key": "value"
    }
}
6. بەشداریکردن لە بابەتێک
Endpoint: POST /subscribe-topic

Request Body:

{
    "tokens": [
        "FCM_TOKEN_1",
        "FCM_TOKEN_2"
    ],
    "topic": "news"
}
7. لادانی بەشداری لە بابەتێک
Endpoint: POST /unsubscribe-topic

Request Body:

{
    "tokens": [
        "FCM_TOKEN_1",
        "FCM_TOKEN_2"
    ],
    "topic": "news"
}
8. هەڵگرتنی FCM Token (پێویستی بە Authentication هەیە)
Endpoint: POST /save-token

Headers:

Authorization: Bearer YOUR_API_TOKEN
Request Body:

{
    "token": "FCM_DEVICE_TOKEN"
}
نموونەی بەکارهێنان بە cURL
ناردنی نۆتیفیکەیشن بۆ ئامێرێک:
curl -X POST http://localhost:8000/api/notifications/send-to-device \
  -H "Content-Type: application/json" \
  -d '{
    "token": "YOUR_FCM_TOKEN",
    "title": "پەیامی تازە",
    "body": "ئەمە نۆتیفیکەیشنێکی تاقیکردنەوەیە",
    "data": {
      "action": "open_page",
      "page_id": "123"
    }
  }'
ناردنی نۆتیفیکەیشن بۆ هەموو بەکارهێنەران:
curl -X POST http://localhost:8000/api/notifications/send-to-all \
  -H "Content-Type: application/json" \
  -d '{
    "title": "هەواڵی گرنگ",
    "body": "هەواڵێکی گرنگ بۆ هەموو بەکارهێنەران"
  }'
نموونەی بەکارهێنان بە JavaScript (Axios)
const axios = require('axios');

async function sendNotification() {
  try {
    const response = await axios.post('http://localhost:8000/api/notifications/send-to-device', {
      token: 'FCM_DEVICE_TOKEN',
      title: 'پەیامی تازە',
      body: 'ئەمە نۆتیفیکەیشنێکی تاقیکردنەوەیە',
      data: {
        action: 'open_page',
        page_id: '123'
      }
    });
    
    console.log('Success:', response.data);
  } catch (error) {
    console.error('Error:', error.response.data);
  }
}

sendNotification();
Response Format
Success Response:

{
    "success": true,
    "message": "Notification sent successfully",
    "result": {...}
}
Error Response:

{
    "success": false,
    "message": "Error message here"
}
تێبینیەکان
پێش بەکارهێنان، دڵنیابە کە فایلی service-account.json لە شوێنی دروست دانراوە
بۆ بەکارهێنانی /save-token، پێویستە Sanctum API authentication دابمەزرێنیت
FCM tokens دەبێت لە کڵاینت (Android/iOS/Web) وەربگیرێت و بنێردرێت بۆ سێرڤەر
بۆ ئەزموونکردنی API، دەتوانیت Postman یان cURL بەکاربهێنیت
چارەسەرکردنی کێشەکان
ئەگەر هەڵەیەکت پێکەوت:

دڵنیابە کە فایلی service-account.json لە شوێنی دروست دانراوە
چێک بکە کە FIREBASE_PROJECT_ID لە .env دروست دانراوە
دڵنیابە کە FCM tokens دروستن و کاتیان بەسەر نەچووە
بڕوانە بە لۆگەکانی Laravel لە storage/logs/laravel.log
