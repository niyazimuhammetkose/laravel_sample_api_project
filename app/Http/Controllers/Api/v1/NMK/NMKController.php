<?php

namespace App\Http\Controllers\Api\v1\NMK;

use App\Http\Controllers\Api\v1\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Niyazimuhammetkose\DemoPhpPackage\HelloWorld;
use OpenApi\Attributes as OA;

class NMKController extends Controller
{
    #[OA\Get(path: '/api/v1/nmk/hello', tags: ['NMK'])]
    #[OA\Response(response: '200', description: 'Hello')]
    #[OA\Response(response: '401', description: 'Unauthorized')]
    #[OA\Response(response: '409', description: 'Conflict')]
    public function hello(Request $request)
    {
        $helloWorld = new HelloWorld();
        $hello = $helloWorld->sayHello();
        return response()->json(["hello" => $hello]);
    }
}
