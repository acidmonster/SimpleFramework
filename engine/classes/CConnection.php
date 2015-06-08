<?php

// Includes
$included_classes = array(
    'CLogger'
);

foreach ($included_classes as $class_name) {
    include_once $class_name.'.php';
}

/**
 * Класс для выполнения запросов
 */
class CConnection {

    protected $server;
    protected $db;
    protected $user;
    protected $pass;
    /**
     *
     * @var mysqli
     */
    protected $mysqli;
    protected $opened;
    protected $has_error;

    function __construct() {
        $this->opened = FALSE;
        $this->has_error = FALSE;
        $this->openConnection();
    }

    function __destruct() {
        $this->closeConention();
    }

    /**
     * Метод открывает соединение с базой данных
     */
    protected function openConnection() {
        // Read the configuration settings from XML file
        $config_path = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "" . CApp::APP_CONFIG_FILE_PATH;
        $XMLConfig = new XMLReader();
        $XMLConfig->open($config_path);

        while ($XMLConfig->read()) {
            switch ($XMLConfig->name) {
                /**
                 *  Get "connection" section for reading database connection attributes
                 */
                case "connection":
                    $this->server = $XMLConfig->getAttribute('server');
                    $this->db = $XMLConfig->getAttribute("dbase");
                    $this->user = $XMLConfig->getAttribute("user");
                    $this->pass = $XMLConfig->getAttribute("pass");

                    // Создать экземпляр класса для подключения к MS SQL
                    $mysqli_conn = new mysqli($this->server, $this->user, $this->pass, $this->db);
                    $mysqli_conn->set_charset("utf8");

                    /**
                     *  Check for errors
                     */
                    if ($mysqli_conn->connect_errno) {
                        $this->has_error = TRUE;
                        CLogger::writeLog('CConnection->openConnection(): Failed to setup connection for user "' . $this->user . '" ' . mysqli_connect_error());
                    } else {
                        $this->mysqli = $mysqli_conn;
                        $this->opened = TRUE;
                    }
                    break;
            }
        }
    }

    /**
     *  Метод закрывает соединение с базой данных
     */
    protected function closeConention() {
        if ($this->opened) {
            $this->mysqli->close();
        }
    }

    /**
     * Метод выполняет SQL-запрос
     * @param string $query_text SQL-запрос
     * @return mysqli_result
     */
    public function query($query_text) {

        /**
         *  Check query text
         */
        if (isset($query_text)) {
            // Execute query
            $this->mysqli->query("SET NAMES 'utf8'");
            $this->mysqli->query("SET CHARACTER SET 'utf8'");
            $this->mysqli->query("SET SESSION collation_connection = 'utf8_general_ci'");

            if ($mysqli_result = $this->mysqli->query($query_text)) {
                return $mysqli_result;
            } else {
                CLogger::writeLog('CConnection->query(): Ошибка при выполнении запроса '.$this->mysqli->error);
            }

        } else {
            CLogger::writeLog('CConnection->query(): Должен быть задан текст запроса.');
            return NULL;
        }
    }

    /**
     * Метод проверяет наличие ошибки при аыполнении SQL-запроса
     * @return boolean
     */
    public function connectError() {
        return $this->has_error;
    }

    /**
     * Метод возвращает ошибку выполнения SQL-запроса
     */

    public function getError() {
        return $this->mysqli->error;
    }

}