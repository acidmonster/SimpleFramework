<?php

include_once 'CBaseFactory.php';
include_once 'CUser.php';
include_once 'CConnection.php';

/**
 * Description of CServiceFactory
 * Сервисная фабрика
 * @author family
 */

class CServiceFactory extends CBaseFactory {

    /**
     * Get User data by ID from ha_users.
     * Return user type
     * @param type int $user_id User ID form ha_users
     */
    public static function getUserByID($id) {
        if (isset($id)) {
            $conn = new CConnection();
            $result = $conn->query("select id, "
                    . "login, "
                    . "reg_date, "
                    . "last_sign_date, "
                    . "user_type, "
                    . "first_name, "
                    . "second_name, "
                    . "last_name, "
                    . "birthdate, "
                    . "comment, "
                    . "state,"
                    . "email from sf_logins where id = '" . $id . "' limit 1");
            unset($conn);

            if ($result->num_rows) {
                $result_data = $result->fetch_object();

                $user = new CUser();
                $user->setId($result_data->id);                             // ИД записи
                $user->setLogin($result_data->login);                       // Логин
                $user->setEmail($result_data->email);                       // Адрес электронной почты
                $user->setRegDate($result_data->reg_date);                  // Дата регистрации в базе данных
                $user->setLastSignDate($result_data->last_sign_date);       // Дата последней авторизации
                $user->setType($result_data->user_type);                    // Тип учетной записи
                $user->setFirstName($result_data->first_name);              // Имя
                $user->setSecondName($result_data->second_name);            // Отчество
                $user->setLastName($result_data->last_name);                // Фамилия
                $user->setComment($result_data->comment);                   // Примечание
                $user->setState($result_data->state);                       // Состояние учетной записи
                $user->setBirthdate($result_data->birthdate);               // Дата рождения

                // Free memory from result data
                $result->close();

                // Return user type
                return $user;
            } else {
                // Free memory from result data
                $result->close();

                return "";
            }
        } else {
            CLogger::writeLog("CServiceFactory::getUserByID(): Не означен ИД пользователя");
            die();
        }
    }

    /**
     * Метод получает текущего пользователя системы
     * @return User Текущий пользователь
     */
    public static function getCurrentUser() {
        $user = $_SESSION[App::APP_CURRENT_USER];
        if (isset($user)) {
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     *
     * @param string $login Логин пользователя
     * @param string $pass Пароль пользователя
     * @param boolean $need_cookie Параметр определяет необходимость создания Cookies
     * @return string Текст ошибки, если она есть.
     */
    public  static function authorizeByLogin($login, $pass = '', $need_cookie = false) {
        $error_message = "";
        if (isset($login)) {
            $conn = new CConnection();

            if (!$conn->connectError()) {
                $query_result = $conn->query("select id from sf_logins where login = '" . $login . "' and password = '" . CApp::getSaltedMD5($pass) . "'");

                if (!empty($query_result)) {
                    if ($query_result->num_rows > 0) {
                        // Free memory
                        $query_data = $query_result->fetch_object();
                        $user_id    = $query_data->id;
                        $query_result->close();

                        // Update user last login date
                        $conn->query("update sf_logins set last_sign_date = NOW() where id = " . $user_id);
                        unset($conn);

                        // Get information about user
                        $user = CServiceFactory::getUserByID($user_id);

                        if (isset($user)) {
                            $_SESSION[CApp::APP_AUTHORIZE]    = "YES";
                            $_SESSION[CApp::APP_CURRENT_USER] = $user;

                            // Если указали "Запомнить на сервере", то создать куки
                            if ($need_cookie) {
                                setcookie("SF_USER_ID", $user_id, time()+60*60*24*30, '/');
                            }

                            // Выполнить переход на страницу по умолчанию
                            $location = "Location: " . CApp::APP_PAGES_DEFAULT_PAGE;
                            header($location);
                        } else {
                            $_SESSION[CApp::APP_AUTHORIZE]    = "NO";
                            $_SESSION[CApp::APP_CURRENT_USER] = NULL;
                            $error_message                    = "Ошибка авторизации. Неправильный логин или пароль.";
                        }
                    } else {
                        $_SESSION[CApp::APP_AUTHORIZE]    = "NO";
                        $_SESSION[CApp::APP_CURRENT_USER] = NULL;
                        $error_message                    = "Ошибка авторизации. Неправильный логин или пароль.";
                    }
                } else {
                    CLogger::writeLog("CConnection::query(): Ошибка при выполнении SQL-запроса.");
                    $error_message = "Ошибка сервера.";
                }
            } else {
                $error_message = "Ошибка сервера.";
            }
        } else
        {
            CLogger::writeLog("CServiceFactory::authorizeByLogin(): Не указан логин пользователя");
            die();
        }

        return $error_message;
    }

    public  static function authorizeByID($user_id) {
        $result = FALSE;
        $error_message = "";

        if (isset($user_id)) {
            $conn = new CConnection();

            if (!$conn->connectError()) {
                $query_result = $conn->query("select id from sf_logins where id = '" . $user_id . "'");

                if (!empty($query_result)) {
                    if ($query_result->num_rows > 0) {
                        // Free memory
                        $query_data = $query_result->fetch_object();
                        $user_id    = $query_data->id;
                        $query_result->free_result();

                        // Update user last login date
                        $conn->query("update sf_logins set last_sign_date = NOW() where id = '" . $user_id . "'");

                        unset($conn);

                        // Get information about user
                        $user = CServiceFactory::getUserByID($user_id);

                        if (isset($user)) {
                            $_SESSION[CApp::APP_AUTHORIZE]    = "YES";
                            $_SESSION[CApp::APP_CURRENT_USER] = $user;

                            $result = TRUE;
                        } else {
                            $_SESSION[CApp::APP_AUTHORIZE]    = "NO";
                            $_SESSION[CApp::APP_CURRENT_USER] = NULL;
                            $location = "Location: " . CApp::APP_PAGES_AUTHORIZE;

                            // Выполнить переход на страницу авторизации
                            header($location);
                        }
                    } else {
                        $_SESSION[CApp::APP_AUTHORIZE]    = "NO";
                        $_SESSION[CApp::APP_CURRENT_USER] = NULL;
                        $location = "Location: " . CApp::APP_PAGES_AUTHORIZE;

                        // Выполнить переход на страницу авторизации
                        header($location);
                    }
                } else {
                    CLogger::writeLog("CConnection::query(): Ошибка при выполнении SQL-запроса.");
                    $_SESSION[CApp::APP_AUTHORIZE]    = "NO";
                    $_SESSION[CApp::APP_CURRENT_USER] = NULL;
                    $location = "Location: " . CApp::APP_PAGES_AUTHORIZE;

                    // Выполнить переход на страницу авторизации
                    header($location);
                }
            } else {
                CLogger::writeLog("CConnection::query(): Ошибка сервера.");
                $_SESSION[CApp::APP_AUTHORIZE]    = "NO";
                $_SESSION[CApp::APP_CURRENT_USER] = NULL;
                $location = "Location: " . CApp::APP_PAGES_AUTHORIZE;

                // Выполнить переход на страницу авторизации
                header($location);
            }


        } else
        {
            CLogger::writeLog("CServiceFactory::authorizeByID(): Не указан ИД пользователя.");
            die();
        }

        return $result;
    }
}