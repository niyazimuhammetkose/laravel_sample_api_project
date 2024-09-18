<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(path: '/api/v1/user', tags: ['Auth'])]
    #[OA\Response(response: '200', description: 'User Info')]
    #[OA\Response(response: '401', description: 'Unauthorized')]
    #[OA\Response(response: '409', description: 'Conflict')]
    public function index(Request $request)
    {
        return $request->user();
    }

    #[OA\Delete(path: '/api/v1/user/delete', tags: ['Auth'])]
    #[OA\Response(response: '200', description: 'User Successfully Deleted')]
    #[OA\Response(response: '401', description: 'Unauthorized')]
    #[OA\Response(response: '404', description: 'Not Found')]
    #[OA\Response(response: '409', description: 'Conflict')]
    public function destroy(Request $request)
    {
        $user = $request->user();

        $user->delete();

        Auth::guard('api')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
