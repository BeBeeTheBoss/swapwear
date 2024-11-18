<?php

function sendResponse($data, $status, $message = "No Message")
{
    $response = [
        'data'    => $data,
        'message' => $message,
        'status' => $status
    ];

    return response()->json($response);
}

function storeImage($imageFile, $path)
{
    $imageName = uniqid() . '_' . time() . '.' . $imageFile->getClientOriginalExtension();
    $imageFile->storeAs('public' . $path, $imageName);

    return $imageName;
}
