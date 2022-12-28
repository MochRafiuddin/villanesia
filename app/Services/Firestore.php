<?php

namespace App\Services;

use Google\Cloud\Firestore\FirestoreClient;

class Firestore
{
    protected static $firestore;

    public static function get()
    {
        if (self::$firestore) {
            return self::$firestore;
        }

        $keyFilePath = config('services.firebase.key_file_path');
        $projectId = config('services.firebase.project_id');
        // dd($keyFilePath);
        self::$firestore = new FirestoreClient([
            'projectId' => $projectId,
            'keyFilePath' => $keyFilePath,
        ]);

        return self::$firestore;
    }
}
