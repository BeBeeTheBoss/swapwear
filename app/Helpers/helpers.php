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

function storeFile($file, $path)
{
    $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
    $file->storeAs('public' . $path, $fileName);

    return $fileName;
}
