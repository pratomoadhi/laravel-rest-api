<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    /**
    * @OA\Get(
    *     path="/api/reset-password/{token}",
    *     operationId="authResetCreate",
    *     tags={"Authentication"},
    *     summary="Reset Password",
    *     description="Reset Password here",
    *     @OA\Parameter(
    *         name="token",
    *         in="path",
    *         description="Reset Password token",
    *         required=true,
    *     ),
    *     @OA\Parameter(
    *         name="email",
    *         in="query",
    *         description="User email",
    *         required=true,
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Reset Password created successfully",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Unprocessable Entity",
    *         @OA\JsonContent()
    *     ),
    *     @OA\Response(response=400, description="Bad request"),
    *     @OA\Response(response=404, description="Email Not Found"),
    * )
    */
    /**
     * Display the password reset view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * @OA\Post(
     * path="/api/reset-password",
     * operationId="authResetPost",
     * tags={"Authentication"},
     * summary="Reset Password",
     * description="Reset Password Here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"token", "email", "password", "password_confirmation"},
     *                 @OA\Property(property="token", type="text"),
     *                 @OA\Property(property="email", type="email"),
     *                 @OA\Property(property="password", type="password"),
     *                 @OA\Property(property="password_confirmation", type="password")
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reset Password Stored Successfully",
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
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
