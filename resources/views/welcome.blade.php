<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Firebase Notification Test</title>
    <style>
        body { 
            font-family: Arial; 
            padding: 50px;
            background: #f5f5f5;
        }
        .box {
            background: white;
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        button {
            background: #4285f4;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin: 10px 0;
        }
        button:hover { background: #357ae8; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        #tokenDisplay {
            background: #f0f0f0;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            word-break: break-all;
            display: none;
            font-family: monospace;
            font-size: 12px;
        }
        .show { display: block !important; }
        h1 { color: #333; text-align: center; }
        h3 { color: #666; margin: 20px 0 10px; }
        .status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            display: none;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>🔔 Firebase Push Notification</h1>
        
        <h3>Step 1: Get FCM Token</h3>
        <button onclick="getToken()">Click to Get Token</button>
        
        <div id="tokenDisplay"></div>
        
        <h3>Step 2: Send Notification</h3>
        <button onclick="sendNotification()" id="sendBtn" disabled>Send Test Notification</button>
        
        <div id="status" class="status"></div>
    </div>

    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"></script>
    <script>
        // Initialize Firebase
        firebase.initializeApp({
            apiKey: "AIzaSyAhTA_8ewqHZOU0Gr18SV4MvNti7MUzMLM",
            authDomain: "notification-send-c5364.firebaseapp.com",
            projectId: "notification-send-c5364",
            storageBucket: "notification-send-c5364.firebasestorage.app",
            messagingSenderId: "735811275057",
            appId: "1:735811275057:web:6630cda6b1073d57864810"
        });

        const messaging = firebase.messaging();
        let currentToken = '';

        async function getToken() {
            try {
                showStatus('Requesting permission...', '#2196F3');
                
                const permission = await Notification.requestPermission();
                
                if (permission === 'granted') {
                    showStatus('Getting token...', '#4CAF50');
                    
                    const token = await messaging.getToken({
                        vapidKey: 'BOzcyGM5rl5bvYnqxreDBxwkyLNuSkRHsr-yy3A3RRSQg5PDRSQNY1YedK3ZR8hgiGvNIxEOAldw0xEONFu5e8w'
                    });
                    
                    currentToken = token;
                    
                    document.getElementById('tokenDisplay').innerHTML = 
                        '<strong>Your FCM Token:</strong><br><br>' + token +
                        '<br><br><button onclick="copyToken()">📋 Copy Token</button>';
                    document.getElementById('tokenDisplay').classList.add('show');
                    document.getElementById('sendBtn').disabled = false;
                    
                    await fetch('/api/notifications/set-user-token', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({token: token})
                    });
                    
                    showStatus('✅ Token retrieved!', '#4CAF50');
                } else {
                    showStatus('❌ Permission denied!', '#f44336');
                }
            } catch (error) {
                showStatus('❌ Error: ' + error.message, '#f44336');
                console.error(error);
            }
        }

        async function sendNotification() {
            if (!currentToken) {
                showStatus('⚠️ Get token first!', '#FF9800');
                return;
            }

            try {
                showStatus('⏳ Sending...', '#2196F3');
                
                const response = await fetch('/api/notifications/send-to-device', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        token: currentToken,
                        title: 'Test Notification',
                        body: 'This was sent from the website!'
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showStatus('✅ Sent successfully!', '#4CAF50');
                } else {
                    showStatus('❌ Failed: ' + data.message, '#f44336');
                }
            } catch (error) {
                showStatus('❌ Error: ' + error.message, '#f44336');
            }
        }

        function copyToken() {
            navigator.clipboard.writeText(currentToken);
            showStatus('✅ Copied!', '#4CAF50');
        }

        function showStatus(message, color) {
            const statusDiv = document.getElementById('status');
            statusDiv.textContent = message;
            statusDiv.style.background = color;
            statusDiv.classList.add('show');
            setTimeout(() => statusDiv.classList.remove('show'), 5000);
        }

        messaging.onMessage((payload) => {
            showStatus('🔔 Notification: ' + payload.notification.title, '#2196F3');
            new Notification(payload.notification.title, {
                body: payload.notification.body
            });
        });
    </script>
</body>
</html>
