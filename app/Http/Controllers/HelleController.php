<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelleController extends Controller
{
    // cette fonction permet juste de tester que l'api fonctionne normalement
    public function hello(){
        return response()->json(
            ['message' => 'Hello World']
        );
    }
}
