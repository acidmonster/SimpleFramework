<?php

include_once '../classes/CApp.php';
include_once '../classes/CConnection.php';
include_once '../classes/CLogger.php';

CApp::initialize();

// Read "Salt" from Configs
$salt = CApp::getConfigProperty('salt', 'value');

// Check authorize
$error_message = '';
$action        = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$login         = filter_input(INPUT_POST, 'SFAuthorizeLogin', FILTER_SANITIZE_STRING);
$pass          = filter_input(INPUT_POST, 'SFAuthorizePassword', FILTER_SANITIZE_STRING);
$remember      = filter_input(INPUT_POST, 'SFAuthorizeRemeber', FILTER_SANITIZE_STRING);

if (!isset($remember)) {
    $remember    = '';
    $need_cookie = false;
} else {
    $remember    = 'checked';
    $need_cookie = true;
}

// Выполнить действие "Авторизация по паролю"
if ($action) {
    switch ($action) {
        case "login":
            if (($login) && (!empty($login))) {
                // Verify that the user in the database
                $error_message = CServiceFactory::authorizeByLogin($login, $pass, $need_cookie);
            } else {
                $error_message = "Для авторизации укажите логин и пароль.";
            }
            break;

        case "exit":
            CApp::closeUserSession();
            break;
    }
}

// Выполнить действие "Авторизация по Coockies"
$cookie_user_id = filter_input(INPUT_COOKIE, 'SF_USER_ID', FILTER_SANITIZE_STRING);

if($cookie_user_id != '') {
    if (CServiceFactory::authorizeByID($cookie_user_id)) {
        $location = "Location: " . CApp::APP_PAGES_DEFAULT_PAGE;

        // Выполнить переход на страницу по умолчанию
        header($location);
    }
}

// Если получили ошибку, то отобразить сообщение
$display_error = 'none';
if ($error_message != '') {
    $display_error = 'block';
}

// Создать объект для заполнения страницы
$page_data = new CPageData();
// Заголовок страницы
$page_data->setTitle("Авторизация.");

include_once 'template.php';
