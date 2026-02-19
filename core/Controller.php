<?php
namespace Core;

class Controller {
    protected const VIEW_EXT = '.hub.php';

    protected function view($path, $data = []) {
        $basePath = dirname(__DIR__);
        $compiler = new ViewCompiler($basePath);
        return $compiler->render($path, $data);
    }

    protected function json($data) {
        header('Content-Type: application/json');
        return json_encode($data);
    }
}