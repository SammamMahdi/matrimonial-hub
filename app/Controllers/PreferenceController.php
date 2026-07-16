<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\View;
use App\Models\Preference;
use App\Support\Vocabulary;

final class PreferenceController
{
    public function edit(Request $request): string
    {
        $user = Auth::requireMember();

        return View::render('pages/preferences', [
            'title'                => 'What you are looking for',
            'preferences'          => Preference::find($user['user_id']),
            'errors'               => Flash::errors(),
            'genders'              => Vocabulary::genders(),
            'religions'            => Vocabulary::religions(),
            'ethnicities'          => Vocabulary::ethnicities(),
            'professionGroups'     => Vocabulary::professionGroups(),
            'maritalStatuses'      => Vocabulary::maritalStatuses(),
            'undergraduateDegrees' => Vocabulary::undergraduateDegrees(),
            'postgraduateDegrees'  => Vocabulary::postgraduateDegrees(),
        ]);
    }

    public function update(Request $request): void
    {
        $user = Auth::requireMember();

        $data = [
            'preferred_gender'         => (string) $request->input('preferred_gender', ''),
            'preferred_religion'       => (string) $request->input('preferred_religion', ''),
            'preferred_ethnicity'      => (string) $request->input('preferred_ethnicity', ''),
            'preferred_profession'     => (string) $request->input('preferred_profession', ''),
            'preferred_marital_status' => (string) $request->input('preferred_marital_status', ''),
            'preferred_education'      => (string) $request->input('preferred_education', ''),
            'min_age'                  => (string) $request->input('min_age', ''),
            'max_age'                  => (string) $request->input('max_age', ''),
            'min_height_cm'            => (string) $request->input('min_height_cm', ''),
            'max_height_cm'            => (string) $request->input('max_height_cm', ''),
            'interests'                => (string) $request->input('interests', ''),
            'hobbies'                  => (string) $request->input('hobbies', ''),
        ];

        $educationOptions = array_keys(Vocabulary::undergraduateDegrees() + Vocabulary::postgraduateDegrees());

        $validator = Validator::make($data)
            ->numericBetween('min_age', 18, 100, 'Minimum age')
            ->numericBetween('max_age', 18, 100, 'Maximum age')
            ->numericBetween('min_height_cm', 100, 250, 'Minimum height')
            ->numericBetween('max_height_cm', 100, 250, 'Maximum height')
            ->maxLength('interests', 500, 'Interests')
            ->maxLength('hobbies', 500, 'Hobbies');

        foreach ([
            'preferred_gender'         => Vocabulary::genders(),
            'preferred_religion'       => Vocabulary::religions(),
            'preferred_ethnicity'      => Vocabulary::ethnicities(),
            'preferred_profession'     => Vocabulary::professions(),
            'preferred_marital_status' => Vocabulary::maritalStatuses(),
        ] as $field => $options) {
            if ($data[$field] !== '') {
                $validator->in($field, array_keys($options), 'That preference');
            }
        }

        if ($data['preferred_education'] !== '') {
            $validator->in('preferred_education', $educationOptions, 'Preferred education');
        }

        // A range that reads backwards is a user mistake worth catching rather
        // than silently returning zero matches.
        if ($data['min_age'] !== '' && $data['max_age'] !== '' && (int) $data['min_age'] > (int) $data['max_age']) {
            $validator->addError('min_age', 'Minimum age cannot be greater than maximum age.');
        }

        if ($data['min_height_cm'] !== '' && $data['max_height_cm'] !== ''
            && (float) $data['min_height_cm'] > (float) $data['max_height_cm']) {
            $validator->addError('min_height_cm', 'Minimum height cannot be greater than maximum height.');
        }

        if ($validator->fails()) {
            Flash::withErrors($validator->errors());
            Flash::withInput($data);
            Flash::add('error', 'Please fix the highlighted fields.');
            Response::redirect('/preferences');
        }

        Preference::save($user['user_id'], $data);
        Flash::add('success', 'Preferences saved — your matches are ranked against these.');
        Response::redirect('/browse');
    }
}
