<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function jsonResponse($data = [], $status = 200){
        return response()->json($data, $status);
    }
}
