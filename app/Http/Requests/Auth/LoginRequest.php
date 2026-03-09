<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            //'captcha'  => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
        // server-side captcha verification (defensive: ensure captcha cannot be bypassed)
        // if (!\Mews\Captcha\Facades\Captcha::check($this->input('captcha'))) {
        //     RateLimiter::hit($this->throttleKey());
        //     throw ValidationException::withMessages([
        //         'captcha' => 'Captcha salah atau kosong.',
        //     ]);
        // }

        $user = \App\Models\User::where('username', $this->input('username'))->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'username' => 'Username tidak ditemukan.',
            ]);
        }

        if (!\Illuminate\Support\Facades\Hash::check($this->input('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'password' => 'Password salah.',
            ]);
        }

        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')) . '|' . $this->ip());
    }
}
