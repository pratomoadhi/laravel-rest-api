<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * @OA\Post(
     * path="/api/login",
     * operationId="authLogin",
     * tags={"Authentication"},
     * summary="User Login",
     * description="Login User here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password"},
     *                 @OA\Property(property="email", type="email"),
     *                 @OA\Property(property="password", type="password")
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login Successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {   
        $request->authenticate();

        $request->session()->regenerate();

        $success['token'] = auth()->user()->createToken('authToken')->accessToken;
        $success['user'] = auth()->user();
        $message = 'Login successfully.';

        $response = [
            'success' => true,
            'data'    => $success,
            'message' => $message,
        ];

        return response()->json($response, 200);

        // return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * @OA\Post(
     * path="/api/logout",
     * operationId="authLogout",
     * tags={"Authentication"},
     * summary="User Logout",
     * description="Logout User here",
     *     @OA\Response(
     *         response=200,
     *         description="Logout Successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // return redirect('/');
        $message = 'Logout successfully.';

        $response = [
            'success' => true,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }
}
