<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Single source of truth for every controlled vocabulary in the app.
 *
 * The original had three different profession lists (136 options on the profile
 * page, 103 in search, a third in registration) and three different ethnicity
 * lists, so 33 professions were selectable but unsearchable. Everything now
 * reads from here, and Validator checks submitted values against these arrays
 * so the ENUM columns can only ever receive values they accept.
 */
final class Vocabulary
{
    /** @return array<string,string> */
    public static function genders(): array
    {
        return [
            'Male'   => 'Male',
            'Female' => 'Female',
            'Other'  => 'Other',
        ];
    }

    /** @return array<string,string> */
    public static function religions(): array
    {
        return [
            'Muslim'    => 'Muslim',
            'Hindu'     => 'Hindu',
            'Christian' => 'Christian',
            'Buddhist'  => 'Buddhist',
            'Jewish'    => 'Jewish',
            'Atheist'   => 'Atheist',
            'Other'     => 'Other',
        ];
    }

    /** @return array<string,string> */
    public static function ethnicities(): array
    {
        return [
            'Bengali'         => 'Bengali',
            'South Asian'     => 'South Asian',
            'Asian'           => 'Asian',
            'Caucasian'       => 'Caucasian',
            'African'         => 'African',
            'Hispanic'        => 'Hispanic',
            'Middle Eastern'  => 'Middle Eastern',
            'Native American' => 'Native American',
            'Pacific Islander' => 'Pacific Islander',
            'Mixed'           => 'Mixed',
            'Other'           => 'Other',
        ];
    }

    /** @return array<string,string> */
    public static function maritalStatuses(): array
    {
        return [
            'Single'   => 'Single',
            'Divorced' => 'Divorced',
            'Widowed'  => 'Widowed',
            'Married'  => 'Married',
        ];
    }

    /** @return array<string,string> */
    public static function complexions(): array
    {
        return [
            'Fair'   => 'Fair',
            'Medium' => 'Medium',
            'Olive'  => 'Olive',
            'Tan'    => 'Tan',
            'Dark'   => 'Dark',
        ];
    }

    /** @return array<string,string> */
    public static function undergraduateDegrees(): array
    {
        return [
            'BSc'     => 'BSc',
            'BA'      => 'BA',
            'BBA'     => 'BBA',
            'BEng'    => 'BEng',
            'BArch'   => 'BArch',
            'LLB'     => 'LLB',
            'MBBS'    => 'MBBS',
            'Diploma' => 'Diploma',
            'None'    => 'None',
        ];
    }

    /** @return array<string,string> */
    public static function postgraduateDegrees(): array
    {
        return [
            'MSc'   => 'MSc',
            'MA'    => 'MA',
            'MBA'   => 'MBA',
            'MArch' => 'MArch',
            'MPhil' => 'MPhil',
            'PhD'   => 'PhD',
            'None'  => 'None',
        ];
    }

    /**
     * Professions grouped by field. Keys are the stored slugs.
     *
     * @return array<string, array<string,string>>
     */
    public static function professionGroups(): array
    {
        return [
            'Technology & IT' => self::labelled([
                'software-engineer', 'data-scientist', 'web-developer', 'mobile-app-developer',
                'cybersecurity-analyst', 'cloud-engineer', 'network-administrator',
                'it-support-specialist', 'ux-ui-designer', 'ai-engineer', 'blockchain-developer',
                'game-developer',
            ]),
            'Healthcare & Medicine' => self::labelled([
                'doctor', 'nurse', 'dentist', 'pharmacist', 'physical-therapist', 'psychologist',
                'medical-research-scientist', 'occupational-therapist', 'radiologist', 'paramedic',
                'optometrist', 'veterinarian',
            ]),
            'Engineering & Architecture' => self::labelled([
                'civil-engineer', 'mechanical-engineer', 'electrical-engineer', 'chemical-engineer',
                'aerospace-engineer', 'environmental-engineer', 'biomedical-engineer',
                'industrial-engineer', 'architect', 'urban-planner', 'structural-engineer',
            ]),
            'Business & Finance' => self::labelled([
                'accountant', 'financial-analyst', 'investment-banker', 'hr-manager',
                'marketing-manager', 'sales-manager', 'business-consultant', 'project-manager',
                'entrepreneur', 'economist', 'real-estate-agent', 'operations-manager',
            ]),
            'Creative Arts & Design' => self::labelled([
                'graphic-designer', 'interior-designer', 'fashion-designer', 'photographer',
                'animator', 'art-director', 'copywriter', 'music-producer', 'video-editor',
                'game-designer', 'illustrator',
            ]),
            'Education & Training' => self::labelled([
                'teacher', 'university-professor', 'school-counselor', 'corporate-trainer',
                'educational-consultant', 'librarian', 'curriculum-developer',
                'special-education-teacher', 'researcher',
            ]),
            'Law & Public Service' => self::labelled([
                'lawyer', 'paralegal', 'judge', 'police-officer', 'firefighter', 'social-worker',
                'politician', 'diplomat', 'probation-officer', 'civil-servant',
            ]),
            'Science & Research' => self::labelled([
                'biologist', 'chemist', 'physicist', 'environmental-scientist', 'geologist',
                'astronomer', 'forensic-scientist', 'marine-biologist', 'meteorologist', 'geneticist',
            ]),
            'Media & Communication' => self::labelled([
                'journalist', 'news-anchor', 'public-relations-specialist', 'content-writer',
                'editor', 'blogger', 'radio-host', 'film-director', 'social-media-manager',
                'podcast-producer',
            ]),
            'Trades & Skilled Labour' => self::labelled([
                'electrician', 'plumber', 'carpenter', 'mechanic', 'welder', 'hvac-technician',
                'truck-driver', 'construction-worker', 'landscaper', 'painter',
            ]),
            'Hospitality & Tourism' => self::labelled([
                'hotel-manager', 'travel-agent', 'chef', 'event-planner', 'flight-attendant',
                'tour-guide', 'restaurant-manager', 'cruise-director',
            ]),
            'Sports & Fitness' => self::labelled([
                'professional-athlete', 'fitness-trainer', 'sports-coach', 'sports-analyst',
                'physical-education-teacher', 'sports-psychologist', 'sports-manager',
            ]),
            'Environment & Sustainability' => self::labelled([
                'environmental-consultant', 'conservation-scientist', 'ecologist',
                'agricultural-scientist', 'renewable-energy-specialist', 'sustainability-coordinator',
                'wildlife-biologist',
            ]),
            'Freelance & Remote' => self::labelled([
                'virtual-assistant', 'freelance-writer', 'freelance-graphic-designer', 'online-tutor',
                'digital-marketing-consultant', 'e-commerce-specialist', 'freelance-software-developer',
            ]),
            'Other' => self::labelled(['student', 'homemaker', 'retired', 'other']),
        ];
    }

    /** Flat slug => label map across every group. @return array<string,string> */
    public static function professions(): array
    {
        $flat = [];

        foreach (self::professionGroups() as $options) {
            $flat += $options;
        }

        return $flat;
    }

    /** The field a profession slug belongs to, used by the match score. */
    public static function professionField(string $slug): ?string
    {
        foreach (self::professionGroups() as $group => $options) {
            if (isset($options[$slug])) {
                return $group;
            }
        }

        return null;
    }

    /** @return list<string> */
    public static function accountStatuses(): array
    {
        return ['Active', 'Inactive', 'Suspended'];
    }

    /** @param list<string> $slugs @return array<string,string> */
    private static function labelled(array $slugs): array
    {
        $out = [];

        foreach ($slugs as $slug) {
            $out[$slug] = ucwords(str_replace('-', ' ', $slug));
        }

        return $out;
    }
}
