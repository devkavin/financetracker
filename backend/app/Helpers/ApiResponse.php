<?php

if (!function_exists('makeApiResponse')) {
    function makeApiResponse($data = null, $message = null, $status = true, $code = 200, $meta = []) {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], $code);
    }
} 