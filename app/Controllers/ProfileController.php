<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\View;
use App\Models\ConnectionRequest;
use App\Models\Profile;
use App\Models\User;
use App\Support\Uploader;
use App\Support\Vocabulary;

final class ProfileController
{
    public function edit(Request $request): string
    {
        $user = Auth::requireMember();
        $full = User::findWithProfile($user['user_id']) ?? $user;

        return View::render('pages/profile-edit', [
            'title'                => 'My profile',
            'user'                 => $full,
            'completeness'         => Profile::completeness($full),
            'errors'               => Flash::errors(),
            'genders'              => Vocabulary::genders(),
            'religions'            => Vocabulary::religions(),
            'ethnicities'          => Vocabulary::ethnicities(),
            'professionGroups'     => Vocabulary::professionGroups(),
            'maritalStatuses'      => Vocabulary::maritalStatuses(),
            'complexions'          => Vocabulary::complexions(),
            'undergraduateDegrees' => Vocabulary::undergraduateDegrees(),
            'postgraduateDegrees'  => Vocabulary::postgraduateDegrees(),
        ]);
    }

    public function update(Request $request): void
    {
        $user = Auth::requireMember();

        $account = [
            'first_name'  => (string) $request->input('first_name', ''),
            'middle_name' => (string) $request->input('middle_name', ''),
            'last_name'   => (string) $request->input('last_name', ''),
            'email'       => (string) $request->input('email', ''),
            'dob'         => (string) $request->input('dob', ''),
            'gender'      => (string) $request->input('gender', ''),
            'religion'    => (string) $request->input('religion', ''),
            'ethnicity'   => (string) $request->input('ethnicity', ''),
            'profession'  => (string) $request->input('profession', ''),
        ];

        $profile = [
            'phone'               => (string) $request->input('phone', ''),
            'road_number'         => (string) $request->input('road_number', ''),
            'street_number'       => (string) $request->input('street_number', ''),
            'building_number'     => (string) $request->input('building_number', ''),
            'secondary_education' => (string) $request->input('secondary_education', ''),
            'higher_secondary'    => (string) $request->input('higher_secondary', ''),
            'undergraduate'       => (string) $request->input('undergraduate', ''),
            'postgraduate'        => (string) $request->input('postgraduate', ''),
            'marital_status'      => (string) $request->input('marital_status', 'Single'),
            'height_cm'           => (string) $request->input('height_cm', ''),
            'weight_kg'           => (string) $request->input('weight_kg', ''),
            'complexion'          => (string) $request->input('complexion', ''),
            'interests'           => (string) $request->input('interests', ''),
            'hobbies'             => (string) $request->input('hobbies', ''),
            'biography'           => (string) $request->input('biography', ''),
            'family_background'   => (string) $request->input('family_background', ''),
        ];

        $validator = Validator::make($account + $profile)
            ->required('first_name', 'First name')->maxLength('first_name', 50, 'First name')
            ->required('last_name', 'Last name')->maxLength('last_name', 50, 'Last name')
            ->maxLength('middle_name', 50, 'Middle name')
            ->required('email', 'Email')->email('email')
            ->required('dob', 'Date of birth')->dateOfBirth('dob', 18)
            ->in('gender', array_keys(Vocabulary::genders()), 'Gender')
            ->in('religion', array_keys(Vocabulary::religions()), 'Religion')
            ->in('ethnicity', array_keys(Vocabulary::ethnicities()), 'Ethnicity')
            ->in('profession', array_keys(Vocabulary::professions()), 'Profession')
            ->in('marital_status', array_keys(Vocabulary::maritalStatuses()), 'Marital status')
            ->numericBetween('height_cm', 100, 250, 'Height')
            ->numericBetween('weight_kg', 25, 300, 'Weight')
            ->maxLength('biography', 2000, 'Biography')
            ->maxLength('family_background', 2000, 'Family background')
            ->maxLength('phone', 30, 'Phone number');

        if ($profile['complexion'] !== '') {
            $validator->in('complexion', array_keys(Vocabulary::complexions()), 'Complexion');
        }

        if ($profile['undergraduate'] !== '') {
            $validator->in('undergraduate', array_keys(Vocabulary::undergraduateDegrees()), 'Undergraduate degree');
        }

        if ($profile['postgraduate'] !== '') {
            $validator->in('postgraduate', array_keys(Vocabulary::postgraduateDegrees()), 'Postgraduate degree');
        }

        if (User::emailExists($account['email'], $user['user_id'])) {
            $validator->addError('email', 'Another account already uses that email.');
        }

        if ($validator->fails()) {
            Flash::withErrors($validator->errors());
            Flash::withInput($account + $profile);
            Flash::add('error', 'Please fix the highlighted fields.');
            Response::redirect('/profile');
        }

        User::updateAccount($user['user_id'], $account);
        Profile::save($user['user_id'], $profile);

        Flash::add('success', 'Your profile has been updated.');
        Response::redirect('/profile');
    }

    public function updatePhoto(Request $request): void
    {
        $user  = Auth::requireMember();
        $photo = $request->file('photo');

        if ($photo === null) {
            Flash::add('error', 'Choose an image first.');
            Response::redirect('/profile');
        }

        $upload = Uploader::storeImage($photo);

        if (!$upload['ok']) {
            Flash::add('error', $upload['message']);
            Response::redirect('/profile');
        }

        $previous = $user['photo'] ?? null;
        User::updatePhoto($user['user_id'], (string) $upload['filename']);

        // Only remove the old file once the new one is safely referenced.
        if (is_string($previous) && $previous !== '') {
            Uploader::delete($previous);
        }

        Flash::add('success', 'Photo updated.');
        Response::redirect('/profile');
    }

    /** Another member's public profile. */
    public function show(Request $request, string $userId): string
    {
        $viewer = Auth::requireMember();
        $person = User::findWithProfile($userId);

        if ($person === null || $person['account_status'] !== 'Active') {
            Response::notFound('That profile is not available.');
        }

        if ($person['user_id'] === $viewer['user_id']) {
            Response::redirect('/profile');
        }

        $relationship = ConnectionRequest::between($viewer['user_id'], $userId);

        return View::render('pages/profile-show', [
            'title'        => full_name($person),
            'person'       => $person,
            'viewer'       => $viewer,
            'relationship' => $relationship,
            'connected'    => ConnectionRequest::areConnected($viewer['user_id'], $userId),
        ]);
    }
}
