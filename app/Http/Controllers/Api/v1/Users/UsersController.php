<?php

namespace App\Http\Controllers\Api\v1\Users;

use App\Http\Controllers\Api\v1\Controller;
use App\Http\Resources\UserResourceCollection;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UsersController extends Controller
{
    #[OA\Get(path: '/api/v1/users', tags: ['Users'])]
    #[OA\Response(response: '200', description: 'List of users')]
    #[OA\Response(response: '404', description: 'Not Found')]
    public function index(Request $request)
    {
        $users = User::with('OAuthProviders')->withTrashed()->paginate($request->query('perPage', 10));

        $response_data = new UserResourceCollection($users);

        return response()->json($response_data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
