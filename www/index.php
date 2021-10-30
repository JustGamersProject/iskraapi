<?php

//Прописываем заголовки
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials', 'true');
header('Access-Control-Allow-Headers: origin, content-type, accept');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Content-Type: application/json; charset=utf8');

//Обьявляем переменные
$json   = array();
$errors = array();

require_once __DIR__ . '/../vendor/autoload.php';

use \Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->usePutenv()->load(dirname(__DIR__).'/.env');

//Подключаем конфиг
include(__DIR__ . "/config/config.php");

include(__DIR__ . "/config/SystemMethods.php");

//Требуемый метод/модуль

if (isset($_GET["unit"], $_GET["operation"]) && count($errors) <= 0) {
    try {
        $filename = __DIR__ . "/api/" . $_GET["unit"] . "/" . $_GET["operation"] . ".php";
        if (file_exists($filename)) {

            $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.'', DB_USER, DB_PASSW); 
            $dbh->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES `utf8`');
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            //Подключаем модуль
            ob_start();
            $response = include($filename);
            ob_get_contents();
            ob_end_clean();
        } else {
            SystemMethods::addError("operation not found");
        }
    } catch (Exception $e) {
        SystemMethods::addError($e->getMessage());
    }
} else {
    SystemMethods::addError("unit or operation not specified");
}

if (count($errors) > 0 || http_response_code() != '200') {
    $json = array(
        'status' => "error",
        'errors' => $errors
    );
} else {
    $json = array(
        'status' => "done",
        'data' => $response
    );
}

echo json_encode($json, JSON_UNESCAPED_UNICODE);