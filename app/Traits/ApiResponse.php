<?php

namespace App\Traits;

/*Instead of writing the same success() and error() functions in every controller,
you write it once in a Trait and just say use ApiResponse in any controller
that needs it.*/

trait ApiResponse {
    protected function success($data, $message = 'OK', $code = 200) {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error($message = 'Error', $code = 400) {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
        ], $code);
    }
}
