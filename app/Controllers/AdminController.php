<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\ActivityLog;
use App\Models\ConnectionRequest;
use App\Models\Message;
use App\Models\User;
use App\Support\Uploader;
use App\Support\Vocabulary;

final class AdminController
{
    public function showLogin(): string
    {
        if (Auth::isAdmin()) {
            Response::redirect('/admin');
        }

        return View::render('admin/login', ['title' => 'Admin sign-in'], 'layouts/blank');
    }

    public function login(Request $request): void
    {
        $username = (string) $request->input('username', '');
        $password = (string) ($request->all()['password'] ?? '');

        $admin = Auth::attemptAdmin($username, $password);

        if ($admin === null) {
            Flash::add('error', 'Those administrator details are not valid.');
            Response::redirect('/admin/login');
        }

        Auth::loginAdmin((int) $admin['admin_id']);
        Response::redirect('/admin');
    }

    public function dashboard(Request $request): string
    {
        Auth::requireAdmin();

        return View::render('admin/dashboard', [
            'title' => 'Admin dashboard',
            'stats' => [
                'total'     => User::countAll(),
                'active'    => User::countByStatus('Active'),
                'suspended' => User::countByStatus('Suspended'),
                'inactive'  => User::countByStatus('Inactive'),
                'new30'     => User::countRegisteredSince(date('Y-m-d', strtotime('-30 days'))),
                'requests'  => ConnectionRequest::countAll(),
                'accepted'  => ConnectionRequest::countByStatus('Accepted'),
                'pending'   => ConnectionRequest::countByStatus('Pending'),
                'messages'  => Message::countAll(),
            ],
            'activity' => ActivityLog::recent(12),
        ], 'layouts/admin');
    }

    public function users(Request $request): string
    {
        Auth::requireAdmin();

        $term   = (string) $request->query('q', '');
        $status = (string) $request->query('status', '');

        if (!in_array($status, Vocabulary::accountStatuses(), true)) {
            $status = '';
        }

        return View::render('admin/users', [
            'title'    => 'Members',
            'users'    => User::search($term, $status),
            'term'     => $term,
            'status'   => $status,
            'statuses' => Vocabulary::accountStatuses(),
        ], 'layouts/admin');
    }

    public function setStatus(Request $request): void
    {
        Auth::requireAdmin();

        $userId = (string) $request->input('user_id', '');
        $status = (string) $request->input('status', '');

        // Whitelist rather than trust: the original interpolated $_POST
        // straight into "UPDATE User SET Account_Status = '$new_status'".
        if (!in_array($status, Vocabulary::accountStatuses(), true)) {
            Flash::add('error', 'Unknown account status.');
            Response::redirect('/admin/users');
        }

        if (User::find($userId) === null) {
            Flash::add('error', 'That member no longer exists.');
            Response::redirect('/admin/users');
        }

        User::setStatus($userId, $status);
        Flash::add('success', 'Account status updated to ' . $status . '.');
        Response::redirect('/admin/users');
    }

    public function deleteUser(Request $request): void
    {
        Auth::requireAdmin();

        $userId = (string) $request->input('user_id', '');
        $user   = User::find($userId);

        if ($user === null) {
            Flash::add('error', 'That member no longer exists.');
            Response::redirect('/admin/users');
        }

        // Remove the photo too, otherwise deleted members leave their pictures
        // on disk forever.
        Uploader::delete($user['photo'] ?? null);
        User::delete($userId);

        Flash::add('success', 'Member deleted.');
        Response::redirect('/admin/users');
    }

    public function logout(): void
    {
        Session::destroy();
        Response::redirect('/admin/login');
    }
}
