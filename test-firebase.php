<?php

use App\Services\FirebaseService;

try {
    $firebase = new FirebaseService();
    echo "✅ Firebase initialized successfully!\n";
    echo "✅ Project ID: " . config('firebase.project_id') . "\n";
    echo "✅ Configuration is correct!\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
