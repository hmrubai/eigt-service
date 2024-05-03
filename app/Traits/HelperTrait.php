<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

trait HelperTrait
{
    protected function successResponse($data, $message, $statusCode = 200): JsonResponse
    {
        $array = [
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($array, $statusCode);
    }

    protected function deleteResponse($message, $statusCode = 200): JsonResponse
    {
        $array = [
            'data' => (object)null,
            'message' => $message,
        ];

        return response()->json($array, $statusCode);
    }

    protected function errorResponse($error, $message, $statusCode): JsonResponse
    {
        $array = [
            'errors' => $error,
            'message' => $message,
        ];

        return response()->json($array, $statusCode);
    }

    protected function fileUpload($fullRequest, $fileName, $destination)
    {
        $file = null;
        $file_url = null;
        if ($fullRequest->hasFile($fileName)) {
            $image = $fullRequest->file($fileName);
            $time = time();
            $file = $fileName.'-'.Str::random(6).$time.'.'.$image->getClientOriginalExtension();
            $destinations = 'uploads/'.$destination;
            $image->move($destinations, $file);
            $file_url = $destination.'/'.$file;
        }

        return $file_url;
    }

    protected function deleteFile($file)
    {
        $image_path = public_path('uploads/'.$file);
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        return true;
    }
}
