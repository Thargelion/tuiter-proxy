<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use function App\Http\Controllers\Auth\config;
use function App\Http\Controllers\Auth\response;

class TuiterApiController
{
    private $defaultResponseHeaders = [
        'Content-Type' => 'application/json',
    ];

    private $defaultRequestHeaders = [];

    private $host = '';

    public function __construct()
    {
        $this->host = config('custom.tuiter_host');
        $this->defaultRequestHeaders['Api-Secret'] = config('custom.tuiter_api_key');
    }

    public function createTuit(Request $request)
    {
        $this->defaultRequestHeaders['Authorization'] = $request->header('Authorization');
        $res = Http::withHeaders($this->defaultRequestHeaders)->post(
            $this->host . '/v1/me/tuits',
            $request->all()
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

    public function feed(Request $request)
    {
        $this->defaultRequestHeaders['Authorization'] = $request->header('Authorization');
        $res = Http::withHeaders($this->defaultRequestHeaders)->get(
            $this->host . '/v1/me/feed',
            $request->query()
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

    public function showTuit(Request $request)
    {
        $this->defaultRequestHeaders['Authorization'] = $request->header('Authorization');
        $tuitId = $request->route('tuit_id');
        $res = Http::withHeaders([
            'Authorization' => $request->header('Authorization')
        ])->get(
            $this->host . '/v1/me/tuits/' . $tuitId
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }


    public function addLike(Request $request)
    {
        $this->defaultRequestHeaders['Authorization'] = $request->header('Authorization');
        $tuitId = $request->route('tuit_id');
        $res = Http::withHeaders($this->defaultRequestHeaders)->post(
            $this->host . '/v1/me/tuits/' . $tuitId . '/likes'
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

    public function removeLike(Request $request)
    {
        $this->defaultRequestHeaders['Authorization'] = $request->header('Authorization');
        $tuitId = $request->route('tuit_id');
        $res = Http::withHeaders($this->defaultRequestHeaders)->delete(
            $this->host . '/v1/me/tuits/' . $tuitId . '/likes'
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

    public function login(Request $request)
    {
        $res = Http::withHeaders($this->defaultRequestHeaders)->post(
            $this->host . '/v1/login',
            $request->all()
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

    public function createUser(Request $request)
    {
        $res = Http::withHeaders($this->defaultRequestHeaders)->post(
            $this->host . '/v1/users',
            $request->all()
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

    public function updateProfile(Request $request)
    {
        $this->defaultRequestHeaders['Authorization'] = $request->header('Authorization');
        $res = Http::withHeaders($this->defaultRequestHeaders)->post(
            $this->host . '/v1/me/profile',
            $request->all()
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

    public function profile(Request $request)
    {
        $this->defaultRequestHeaders['Authorization'] = $request->header('Authorization');
        $res = Http::withHeaders($this->defaultRequestHeaders)->get(
            $this->host . '/v1/me/profile'
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }
}
