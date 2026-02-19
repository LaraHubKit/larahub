<?php
namespace Core;

class Controller {
    protected function view($path, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . "/../views/$path.php";
        return ob_get_clean();
    }

    protected function json($data) {
        header('Content-Type: application/json');
        return json_encode($data);
    }
}