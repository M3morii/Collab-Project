<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

class EmailVerificationController extends Controller
{
    public function sendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email sudah diverifikasi'
            ], 200);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Link verifikasi telah dikirim'
        ], 200);
    }

    public function verify(Request $request)
    {
        // Cari user berdasarkan id dari URL
        $user = User::find($request->route('id'));

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Validasi hash signature
        if (!hash_equals(
            (string) $request->route('hash'),
            sha1($user->getEmailForVerification())
        )) {
            return response()->json([
                'message' => 'URL verifikasi tidak valid'
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email sudah diverifikasi'
            ], 200);
        }

        try {
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }

            return response()->json([
                'message' => 'Email berhasil diverifikasi'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memverifikasi email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 