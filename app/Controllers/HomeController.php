<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\ConnectionRequest;
use App\Models\User;

final class HomeController
{
    public function index(Request $request): string
    {
        if (Auth::check()) {
            Response::redirect('/dashboard');
        }

        // Real numbers for the landing page counters. The original hard-coded
        // "102 profile views" and similar into the template.
        $stats = [
            'members' => User::countByStatus('Active'),
            'matches' => (int) (ConnectionRequest::countByStatus('Accepted') / 2) * 2,
            'joined'  => User::countRegisteredSince(date('Y-m-d', strtotime('-30 days'))),
        ];

        return View::render('pages/landing', [
            'title' => 'Matrimonial Hub — Find someone who fits your life',
            'stats' => $stats,
        ], 'layouts/public');
    }

    public function about(): string
    {
        return View::render('pages/about', ['title' => 'About us'], 'layouts/public');
    }

    public function stories(): string
    {
        return View::render('pages/stories', ['title' => 'Client stories'], 'layouts/public');
    }

    public function help(): string
    {
        return View::render('pages/help', ['title' => 'Help centre'], 'layouts/public');
    }

    public function privacy(): string
    {
        return View::render('pages/privacy', ['title' => 'Privacy policy'], 'layouts/public');
    }

    public function terms(): string
    {
        return View::render('pages/terms', ['title' => 'Terms and conditions'], 'layouts/public');
    }
}
