<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\View;
use App\Models\ActivityLog;
use App\Models\User;
use App\Support\Uploader;
use App\Support\Vocabulary;

final class AuthController
{
    public function showLogin(): string
    {
        if (Auth::check()) {
            Response::redirect('/dashboard');
        }

        return View::render('pages/login', [
            'title'  => 'Sign in',
            'errors' => Flash::errors(),
        ], 'layouts/public');
    }

    public function login(Request $request): void
    {
        $email    = (string) $request->input('email', '');
        $password = (string) ($request->all()['password'] ?? '');

        if ($email === '' || $password === '') {
            Flash::add('error', 'Enter both your email and password.');
            Flash::withInput(['email' => $email]);
            Response::redirect('/login');
        }

        $user = Auth::attempt($email, $password);

        if ($user === null) {
            // One message for both "no such email" and "wrong password", so the
            // form cannot be used to discover which emails are registered.
            Flash::add('error', 'Those details do not match an account.');
            Flash::withInput(['email' => $email]);
            Response::redirect('/login');
        }

        if ($user['account_status'] !== 'Active') {
            Flash::add('error', 'This account is ' . strtolower($user['account_status']) . '. Please contact support.');
            Response::redirect('/login');
        }

        Auth::login($user['user_id']);
        User::touchLastSeen($user['user_id']);
        ActivityLog::record($user['user_id'], 'Signed in', $request->ip());

        Response::redirect('/dashboard');
    }

    public function showRegister(): string
    {
        if (Auth::check()) {
            Response::redirect('/dashboard');
        }

        return View::render('pages/register', [
            'title'            => 'Create your profile',
            'errors'           => Flash::errors(),
            'genders'          => Vocabulary::genders(),
            'religions'        => Vocabulary::religions(),
            'ethnicities'      => Vocabulary::ethnicities(),
            'professionGroups' => Vocabulary::professionGroups(),
        ], 'layouts/public');
    }

    public function register(Request $request): void
    {
        $data = [
            'first_name'  => (string) $request->input('first_name', ''),
            'middle_name' => (string) $request->input('middle_name', ''),
            'last_name'   => (string) $request->input('last_name', ''),
            'email'       => (string) $request->input('email', ''),
            'dob'         => (string) $request->input('dob', ''),
            'gender'      => (string) $request->input('gender', ''),
            'religion'    => (string) $request->input('religion', ''),
            'ethnicity'   => (string) $request->input('ethnicity', ''),
            'profession'  => (string) $request->input('profession', ''),
            'nid'         => (string) $request->input('nid', ''),
            'password'    => (string) ($request->all()['password'] ?? ''),
        ];

        $validator = Validator::make($data + ['password_confirmation' => $request->all()['password_confirmation'] ?? ''])
            ->required('first_name', 'First name')->maxLength('first_name', 50, 'First name')
            ->required('last_name', 'Last name')->maxLength('last_name', 50, 'Last name')
            ->maxLength('middle_name', 50, 'Middle name')
            ->required('email', 'Email')->email('email')->maxLength('email', 255, 'Email')
            ->required('dob', 'Date of birth')->dateOfBirth('dob', 18)
            ->required('gender', 'Gender')->in('gender', array_keys(Vocabulary::genders()), 'Gender')
            ->required('religion', 'Religion')->in('religion', array_keys(Vocabulary::religions()), 'Religion')
            ->required('ethnicity', 'Ethnicity')->in('ethnicity', array_keys(Vocabulary::ethnicities()), 'Ethnicity')
            ->required('profession', 'Profession')->in('profession', array_keys(Vocabulary::professions()), 'Profession')
            ->required('nid', 'National ID')->maxLength('nid', 30, 'National ID')
            ->required('password', 'Password')->minLength('password', 8, 'Password')
            ->matches('password_confirmation', 'password', 'Password confirmation');

        if (User::emailExists($data['email'])) {
            $validator->addError('email', 'An account already uses that email address.');
        }

        // Photo is optional at signup — a generated avatar stands in until they
        // upload one, so a failed upload can never block registration.
        $photoName = null;
        $photo     = $request->file('photo');

        if ($photo !== null) {
            $upload = Uploader::storeImage($photo);

            if (!$upload['ok']) {
                $validator->addError('photo', $upload['message']);
            } else {
                $photoName = $upload['filename'];
            }
        }

        if ($validator->fails()) {
            Flash::withErrors($validator->errors());
            Flash::withInput($data);
            Flash::add('error', 'Please fix the highlighted fields.');
            Response::redirect('/register');
        }

        try {
            $userId = User::create($data + ['photo' => $photoName]);
        } catch (\Throwable $e) {
            // Don't leave an orphaned upload behind if the insert fails.
            if ($photoName !== null) {
                Uploader::delete($photoName);
            }

            throw $e;
        }

        Auth::login($userId);
        Flash::add('success', 'Welcome to Matrimonial Hub. Tell us who you are looking for to get matches.');
        Response::redirect('/preferences');
    }

    public function logout(): void
    {
        $userId = Auth::id();

        if ($userId !== null) {
            ActivityLog::record($userId, 'Signed out');
        }

        Auth::logout();
        Flash::add('success', 'You have been signed out.');
        Response::redirect('/');
    }
}
