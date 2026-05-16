<?php
declare(strict_types=1);

Auth::requireLogin();

if (Auth::mustChangePassword()) {
    Response::redirect(Auth::changePasswordUrl());
}