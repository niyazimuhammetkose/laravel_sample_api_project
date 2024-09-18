<?php

namespace App\Http\Controllers\Tokens;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tokens\CreateTokenRequest;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

class TokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json($request->user()->tokens);
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
    public function store(CreateTokenRequest $request)
    {
        $permissions = !isEmpty($request->get('permissions')) ?: ['*'];
        $expiresAt = !isEmpty($request->get('permissions')) ? now()->addMinutes(60) : null;

        $token = $request->user()->createToken($request->get('token_name'), $permissions, $expiresAt);

        return response()->json(['token' => $token->plainTextToken], 201);
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
    public function destroy(Request $request, string $id)
    {
        $request->user()->tokens()->where('id', $id)->delete();

        return response()->noContent();
    }
}
