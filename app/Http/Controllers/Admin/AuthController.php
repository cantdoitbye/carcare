<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Contracts\AdminUserRepositoryInterface;


class AuthController extends Controller
{
   public function __construct(
        protected AdminUserRepositoryInterface $adminUserRepository
    ) {}

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin->isActive()) {
                Auth::guard('admin')->logout();
                return back()->withErrors(['email' => 'Your account is inactive.']);
            }

            $this->adminUserRepository->updateLastLogin($admin->id);
            
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
