<?php
require_once 'functions.php';
require_once __DIR__ . '/../libraries/Psr4AutoloaderClass.php';

$loader = new Psr4AutoloaderClass;
$loader->register();

// Các lớp có không gian tên bắt đầu với CT275\Labs nằm ở src/classes
$loader->addNamespace('CT275\Labs', __DIR__ . '/classes');

try {
    $PDO = (new CT275\Labs\PDOFactory())->create([
        'dbhost' => 'localhost',
        'dbname' => 'ct275_lab4',
        'dbuser' => 'root',
        'dbpass' => '1242003'
    ]);
} catch (Exception $ex) {
    echo 'Không thể kết nối đến MySQL,
kiểm tra lại username/password đến MySQL.<br>';
    exit("<pre>${ex}</pre>");
}
