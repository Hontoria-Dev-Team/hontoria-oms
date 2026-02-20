<?php
require_once __DIR__ . '/../../Config/config.php';
require_once __DIR__ . '/../Controllers/AuthorizationC.php';

$controller = new AuthorizationC($pdo);

$controller->showPage();
