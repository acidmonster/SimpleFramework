<?php

/**
 * Класс для заполнения параметров страницы
 */
class CPageData {

    protected $title;
    protected $content;


    function __construct() {
        $this->title = "";
        $this->content = "";
    }

    /**
     *
     * @method string getTitle(void)
     * @method string getContent(void)
     * @method string getInfoDisplay(void)
     * @method string setTitle($value)
     * @method string setContent($value)
     * @method string setInfoDisplay($value)
     */
    public function __call($method_name, $arguments) {
        $args = preg_split('/(?<=\w)(?=[A-Z])/', $method_name);
        $action = array_shift($args);
        $property_name = strtolower(implode('_', $args));

        switch ($action) {
            case 'get':
                return isset($this->$property_name) ? $this->$property_name : null;

            case 'set':
                $this->$property_name = $arguments[0];
                return $this;
        }
    }
}