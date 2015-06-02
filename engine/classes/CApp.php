<?php

// Includes
$included_classes = array(
    'CLogger',
    'CServiceFactory',
    'CPageData',
    'CBaseList'
);

foreach ($included_classes as $class_name) {
    include_once $class_name . '.php';
}

/**
 * Класс приложения
 */
class CApp {

    //====================================================
    /**
     * Страница авторизации
     */
    const APP_PAGES_AUTHORIZE = '/engine/authorize/';

    /**
     * Страница по умолчанию
     */
    const APP_PAGES_DEFAULT_PAGE = '/';

    /**
     * Путь к лог-файлу
     */
    const APP_LOG_FILE_PATH = '/engine/log/system.log';

    /**
     * Конфигурационный файл приложения
     */
    const APP_CONFIG_FILE_PATH = '/engine/config/appSettings.xml';

    /**
     * Текущая страница
     */
    const APP_PAGES_CURRENT_PAGE = 'app_pages_current_page';

    /**
     * Предыдущая страница
     */
    const APP_PAGES_PREVIUS_PAGE = 'app_pages_previus_page';

    /**
     * Переменная для проверки авторизации пользователя
     */
    const APP_AUTHORIZE = 'app_authorize';

    /**
     * Текущий пользователь
     */
    const APP_CURRENT_USER = 'app_current_user';

    /**
     * Модули расширения
     */
    const APP_EXTENSIONS = 'app_extensions';

    /**
     * Текущий индекс модуля расширения
     */
    const APP_EXT_CURRENT_INDEX = 'app_ext_current_index';

    /**
     * Текущий индекс подсистемы
     */
    const APP_SUB_CURRENT_INDEX = 'app_sub_current_index';

    /**
     * Часовой пояс
     */
    const APP_DEFAULT_TIME_ZONE = 'Europe/Samara';

    /**
     * Путь к папке шаблонов
     */
    const APP_TEMPLATES_PATH = '/engine/templates/';

    /**
     * Путь к папке компонент
     */
    const APP_COMPONENTS_PATH = '/engine/components/';

    /**
     * Наименование сессионной переменной подключения к БД
     */
    const APP_CONNECTION = 'app_connection';

    //==========================================================
    /**
     * Метод возвращает корневую папку сайта
     * @return string
     */
    public static function GetRootFolder() {
        $root_path = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
        /* @var $root_path string возвращает корневую папку сайта */
        return $root_path;
    }

    /**
     * Метод возвращает полный путь до текущего каталога
     * @return string
     */
    public static function getFullCurrentPath() {
        $full_path = dirname(CApp::GetRootFolder() . "" . CAPP::getCurrentPage()) . "/";
        return $full_path;
    }

    /**
     * Метод создает сессию пользователя
     */
    public static function sessionStart() {
        session_start();
    }

    /**
     * Метод закрывает пользовательскую сессию
     */
    public static function closeUserSession() {
        unset($_SESSION[self::APP_AUTHORIZE]);
        unset($_SESSION[self::APP_CURRENT_USER]);
        unset($_SESSION[self::APP_EXTENSIONS]);
    }

    /**
     * Метод выполняет проверку авторизации и возвращает истину,
     * если пользователь авторизован, и ложь в противном случае
     */
    public static function checkAuthorize() {
        if ($_SESSION[self::APP_AUTHORIZE] == "YES") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Метод выполняет проверку авторизации.
     * Если сессия авторизации еще не существует,
     * то перевести пользователя на страниц авторизации.
     */
    public static function needAuthorize() {
        if ($_SESSION[self::APP_AUTHORIZE] != "YES") {
            // Выполнить переход на страницу по умолчанию
            $location = "Location: " . CApp::APP_PAGES_AUTHORIZE;
            header($location);
        }
    }

    /**
     * Метод получает предыдущую страницу, с которой пришел пользователь
     * @return sting
     */
    public static function getPreviusPage() {
        return $_SESSION[self::APP_PAGES_PREVIUS_PAGE];
    }

    /**
     * Метод получает текущую страницу
     * @return sting
     */
    public static function getCurrentPage() {
        return $_SESSION[self::APP_PAGES_CURRENT_PAGE];
    }

    /**
     * Метод устанавливает текущую страницу.
     */
    private static function setCurrentPage($page) {
        if (isset($_SESSION[self::APP_PAGES_CURRENT_PAGE])) {
            $_SESSION[self::APP_PAGES_PREVIUS_PAGE] = $_SESSION[self::APP_PAGES_CURRENT_PAGE];
        } else {
            $_SESSION[self::APP_PAGES_PREVIUS_PAGE] = '';
        }

        $_SESSION[self::APP_PAGES_CURRENT_PAGE] = $page;
    }

    /**
     * Метод возвращает уникальный идентификатор
     * @return string GUID
     */
    public static function getGUID() {
        mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45); // "-"
        $uuid   = substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);
        return $uuid;
    }

    /**
     * Function read config property
     * @param string $property Property name
     * @param string $attribute Attribute name
     * @return string Readed atribute value
     */
    public static function getConfigProperty($property, $attribute = '') {
        $value       = "";
        $config_path = self::GetRootFolder() . "" . self::APP_CONFIG_FILE_PATH;
        $XMLConfig   = new XMLReader();
        $XMLConfig->open($config_path);

        while ($XMLConfig->read()) {
            switch ($XMLConfig->name) {
                case $property:
                    if (!empty($attribute)) {
                        $value = $XMLConfig->getAttribute($attribute);
                    } else {
                        $value = $XMLConfig->readString();
                    }
                    break;
            }
        }

        return $value;
    }

    /**
     * Function transform password to 'salted' MD5
     * @param string $pass Password
     * @return string Salted MD5
     */
    public static function getSaltedMD5($pass) {
        $salt        = self::getConfigProperty('salt', 'value');
        $salted_pass = md5($salt . md5($pass));
        return $salted_pass;
    }

    /**
     * Функция считывает значение GET-параметра
     * @param string $property Наименование параметра
     * @return string Значение параметра. Если параметр не найден, то возвращается пустая строка.
     */
    public static function readURLProperty($property) {
        // Прочитать GET-параметр
        if (isset($property)) {
            $property_value = filter_input(INPUT_GET, $property);

            if (isset($property_value)) {
                return $property_value;
            }
        }

        return NULL;
    }

    /**
     * Метод возвращает путь файла-шаблона
     * @param string $template_name
     * @param CPageData $page_data
     */
    public static function loadTemplateByName($template_name, $page_data) {
        if (isset($template_name)) {
            // Получить путь к файлу шаблона
            $template_path = CApp::APP_TEMPLATES_PATH . $template_name . '/template.php';

            // Проверить наличие файла
            if (!file_exists(CApp::GetRootFolder() . "" . $template_path)) {
                CLogger::writeLog('CApp->getTemplateByName: Не найден шаблон с наименованием "' . $template_name . '"');
                die();
            }

            // Загрузить шаблон
            include CApp::GetRootFolder() . "" . $template_path;
        } else {
            CLogger::writeLog("CApp->getTemplateByName: не передано имя шаблона.");
            die();
        }
    }

    /**
     * Метод рендерит компонент и возвращает код HTML
     * @param string $component_name Имя компонента, который необходимо загрузить
     * @return string HTML код компонента
     */
    public static function loadComponent($component_name) {
        if (isset($component_name)) {
            $component_full_path = CApp::GetRootFolder() . "" . CApp::APP_COMPONENTS_PATH . "" . $component_name . "/component.php";
            // Попытаться найти компонент с переданным имененем.
            if (!file_exists($component_full_path)) {
                CLogger::writeLog("CApp->loadComponent: Компонент с наименованием '" . $component_name . "' не найден.");
                die();
            } else {
                include_once $component_full_path;
                $class_name = "C" . $component_name;
                $component  = new $class_name();
                return $component->render();
            }
        } else {
            CLogger::writeLog("CApp->loadComponent: не передано наименование компонента.");
            die();
        }
    }

    protected static function connectToDB() {
        if (!isset($_SESSION[self::APP_CONNECTION])) {
            $con = new CConnection();
            $_SESSION[self::APP_CONNECTION] = $con;
        }
    }

    protected static function reconnectToDB() {
        $con = new CConnection();
        $_SESSION[self::APP_CONNECTION] = $con;
    }

    protected static function disconnectFromDB() {
        unset($_SESSION[self::APP_CONNECTION]);
    }

    public static function getConnection() {
        if (!isset($_SESSION[self::APP_CONNECTION])) {
            self::connectToDB();
        }

        return $_SESSION[self::APP_CONNECTION];
    }

    public static function initialize() {
        // Установить часовой пояс
        date_default_timezone_set(self::APP_DEFAULT_TIME_ZONE);

        // Открыть сессию
        self::sessionStart();

        if (!isset($_SESSION[self::APP_AUTHORIZE])) {
            $_SESSION[self::APP_AUTHORIZE] = 'NO';
        }

        // Установить текущую страницу для SESSION
        $page_name = filter_input(INPUT_SERVER, 'PHP_SELF');
        self::setCurrentPage($page_name);

        // Проверить наличие Cookie
        $cookie_user_id = filter_input(INPUT_COOKIE, 'SF_USER_ID', FILTER_SANITIZE_STRING);

        if ($cookie_user_id != '') {
            CServiceFactory::authorizeByID($cookie_user_id);
        }

        // Инициализировать подключение к БД
//        self::connectToDB();
    }

}
