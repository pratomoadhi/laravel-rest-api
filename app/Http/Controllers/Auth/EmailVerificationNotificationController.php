<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
     /**
     * @OA\Post(
     * path="/api/email/verification-notification",
     * operationId="authVerifySend",
     * tags={"Authentication"},
     * summary="Send Verification Link",
     * description="Send Verification Link here",
     *     @OA\Response(
     *         response=200,
     *         description="Verification Link Sent",
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
     * Send a new email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            // return redirect()->intended(RouteServiceProvider::HOME);
            $success['user'] = auth()->user();
            $message = 'Email has already been verified';
            $response = [
                'success' => true,
                'data'    => $success,
                'message' => $message,
            ];
            return response()->json($response, 200);
        }

        $request->user()->sendEmailVerificationNotification();

        $message = 'Verification Link Sent';
        $response = [
            'success' => true,
            'message' => $message,
        ];
        return response()->json($response, 200);

        // return back()->with('status', 'verification-link-sent');
    }
}
