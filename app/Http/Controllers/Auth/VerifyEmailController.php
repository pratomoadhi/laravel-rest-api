<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    /**
    * @OA\Get(
    *     path="/api/verify-email/{id}/{hash}",
    *     operationId="authVerify",
    *     tags={"Authentication"},
    *     summary="Verify Email",
    *     description="Verify Email here",
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="User id",
    *         required=true,
    *     ),
    *     @OA\Parameter(
    *         name="hash",
    *         in="path",
    *         description="Verification hash",
    *         required=true,
    *     ),
    *     @OA\Parameter(
    *         name="expires",
    *         in="query",
    *         description="Verification expires at",
    *         required=true,
    *     ),
    *     @OA\Parameter(
    *         name="signature",
    *         in="query",
    *         description="Verification signature",
    *         required=true,
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Verified email successfully",
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
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            // return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
            $success['user'] = auth()->user();
            $message = 'Email has already been verified';
            $response = [
                'success' => true,
                'data'    => $success,
                'message' => $message,
            ];
            return response()->json($response, 200);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        $success['user'] = auth()->user();
        $message = 'Verified email successfully.';

        $response = [
            'success' => true,
            'data'    => $success,
            'message' => $message,
        ];

        return response()->json($response, 200);

        // return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
    }
}
