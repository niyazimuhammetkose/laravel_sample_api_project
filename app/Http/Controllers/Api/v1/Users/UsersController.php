<?php

namespace App\Http\Controllers\Api\v1\Users;

use App\Http\Controllers\Api\v1\Controller;
use App\Http\Resources\UserResourceCollection;
use App\Http\Resources\UserResourceElasticCollection;
use App\Models\User;
use Elastic\ScoutDriverPlus\Support\Query;
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

    #[OA\Get(path: '/api/v1/users/search', tags: ['Users'])]
    #[OA\Response(response: '200', description: 'List of users matching search')]
    #[OA\Response(response: '404', description: 'Not Found')]
    public function search(Request $request)
    {
        $search = $request->query('search');

        $query=Query::multiMatch()
            ->fields(['name', 'email'])
            ->query($search)
            ->fuzziness('AUTO')
        ;

        $query_result = User::searchQuery($query)
            ->paginate()
            ->appends(['search' => $search])
        ;
        $query_result->models()->load('OAuthProviders');

        $response_data = new UserResourceElasticCollection($query_result);

        //region Search With Laravel Scout > Elastic
//        $query = User::search($search)
//            ->withTrashed();
//
//        $users = $query->get();
//
//        $users->load('OAuthProviders');
//
//        $response_data = $users;
        //endregion

        return response()->json($response_data);
    }
}
