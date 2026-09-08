<?php
use App\Models\DeviceList;
use App\Models\User;
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

//curl service function
function curl_service($headers, $dataString, $url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

    $response = curl_exec($ch);

    curl_close($ch);

    return $response;
}

//send notification function
function send_notification_FCM($title, $message, $userId = null)
{
    // $firebaseToken = DeviceList::when($userId, function ($query) use ($userId) {
    //     $query->where('user_id', $userId);
    // })->whereNotNull('device_token')->pluck('device_token')->toArray();

    $firebaseToken = User::when($userId, function ($query) use ($userId) {
        $query->where('id', $userId);
    })->whereNotNull('device_token')->pluck('device_token')->toArray();

    if (empty($firebaseToken)) {
        return ['status' => 'error', 'message' => 'No device tokens found.'];
    }

    $accessToken = get_fcm_bearer_token();
    $projectId = config('app.project_id');
    $url = "https://fcm.googleapis.com/v1/projects/" . $projectId . "/messages:send";

    $data = [
        "message" => [
            "notification" => [
                "title" => $title,
                "body" => $message,
            ]
        ]
    ];

    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
    ];

    $firebaseToken = array_unique($firebaseToken);

    foreach ($firebaseToken as $token) {

        $data['message']['token'] = $token;

        $dataString = json_encode($data);

        $response = curl_service($headers, $dataString, $url);
    }

    return json_decode($response, true);
}

if (!function_exists('get_fcm_bearer_token')) {
    function get_fcm_bearer_token()
    {
        $credentialsJson = config('app.GOOGLE_APPLICATION_CREDENTIALS');

        $credentialsArray = json_decode($credentialsJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON in FIREBASE_CREDENTIALS_JSON');
        }

        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $creds = new ServiceAccountCredentials($scopes, $credentialsArray);
        $creds->fetchAuthToken();
        $token = $creds->getLastReceivedToken();

        return $token['access_token'];
    }
}
