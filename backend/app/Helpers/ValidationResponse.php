<?php

/**
 * Custom error response
 */
function errorResponse($code, $message, $httpCode)
{
    return response()->json([
        'code'=> $code,
        'message'=> $message
    ], $httpCode);
}

?>
