<?php

use Illuminate\Support\Facades\Validator;

$input = Validator::validate(request()->post(), [
    'username' => 'required|string|exists:mysql_legacy.UserAccounts,User|alpha_num|min:4|max:20',
    'token' => 'required',
    'password' => 'required|confirmed|min:8|different:username',
]);

$user = $input['username'];
$passResetToken = $input['token'];
$newPass = $input['password'];

if (!isValidPasswordResetToken($user, $passResetToken)) {
    return back()->withErrors(__('legacy.error.token'));
}

RemovePasswordResetToken($user);

if (changePassword($user, $newPass)) {
    // Perform auto-login:
    authenticateFromCookie($user, $permissions, $userDetails);
    generateAppToken($user, $tokenInOut);

    return back()->with('success', __('legacy.success.password_change'));
}

return back()->withErrors(__('legacy.error.error'));