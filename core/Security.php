<?php
namespace Core;

class Security {
    public static function e($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public static function csrfField() {
        return '<input type="hidden" name="csrf" value="'.$_SESSION['csrf'].'">';
    }

    public static function verifyCsrf($token) {
        if ($token !== ($_SESSION['csrf'] ?? null)) {
            die('CSRF token mismatch');
        }
    }
}