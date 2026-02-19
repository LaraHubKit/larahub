<?php
require __DIR__ . '/../bootstrap/app.php';

$router = new Core\Router();

require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/api.php';

$router->dispatch();