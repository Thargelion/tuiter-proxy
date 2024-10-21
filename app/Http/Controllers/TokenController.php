<?php

namespace App\Http\Controllers;

use App\Models\ApplicationToken;
use App\Models\User;
use App\Services\TokenCrud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TokenController extends Controller
{
    private TokenCrud $tokenCrud;

    public function __construct(TokenCrud $tokenCrud)
    {
        $this->tokenCrud = $tokenCrud;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function userTokens(Request $request)
    {
        $userId = $request->route('user');
        $tokens = ApplicationToken::where('user_id', $userId)->get();
        return response()->json($tokens, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(Request $request)
    {
        $rules = array(
            'email' => 'required|email'
        );
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $token = $this->tokenCrud->createAndStore($request->email);

        return response()->json($token, 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $this->tokenCrud->createAndStore($user->getEmailForVerification());

        return redirect()->route('dashboard');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function delete(Request $request)
    {
        $token = ApplicationToken::find($request->route('token_id'));
        $token->delete();

        return redirect()->route('dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
