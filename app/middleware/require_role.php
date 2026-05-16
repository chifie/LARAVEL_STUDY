<?php
declare(strict_types=1);

if (!isset($allowedRoles) || !is_array($allowedRoles) || empty($allowedRoles)) {
    throw new RuntimeException('Allowed roles must be provided before including require_role.php');
}

Auth::requireRole($allowedRoles);