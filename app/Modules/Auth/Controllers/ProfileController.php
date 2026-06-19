<?php

namespace App\Modules\Auth\Controllers;

use App\Core\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function edit()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults(), 'different:current_password'],
        ], [
            'current_password.current_password' => 'Password saat ini yang Anda masukkan salah.',
            'password.different' => 'Password baru harus berbeda dengan password Anda saat ini.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $oldValues = [
            'password' => '[protected]',
        ];

        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Log to Audit Log
        $this->auditLogService->logUpdated('User', $user->id, $oldValues, [
            'password' => '[protected]',
            'message' => 'User changed password via profile settings.',
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Password Anda berhasil diperbarui.');
    }
}
