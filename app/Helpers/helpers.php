<?php

if (!function_exists('getResponseStructure')) {
    function getResponseStructure($data, $success = true, $message = ''): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'data' => $data
        ];
    }
}
