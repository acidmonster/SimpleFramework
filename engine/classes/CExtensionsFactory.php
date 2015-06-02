<?php

include_once './CBaseFactory.php';

/**
 * Фабрика модулей расширения
 */
class CExtensionsFactory extends baseFactory {

    protected $extensions;

    /**
     * Метод устанавливает текущий индекс модуля расширения
     * @param int $ext_index
     */
    protected function setCurrentExtIndex($ext_index) {
        if (isset($ext_index)) {
            // Если передан допустимый индекс, то установить его
            if ($ext_index >= 0 && $ext_index < $this->extensions->getCount()) {
                $_SESSION[App::APP_EXT_CURRENT_INDEX] = $ext_index;
            }
        }
    }

    /**
     * Метод устанавливает текущий индекс подсистемы
     * @param type $sub_index
     */
    protected function setCurrentSubIndex($sub_index) {
        if (isset($sub_index)) {
            // Если передан допустимый индекс, то установить его
            if ($sub_index >= 0 && $sub_index < $this->extensions->getCount()) {
                $_SESSION[App::APP_SUB_CURRENT_INDEX] = $sub_index;
            }
        }
    }

    public function getCurrentExtIndex() {
        // Получить текущее имя страницы
        $page_path = filter_input(INPUT_SERVER, "PHP_SELF");
        $info = pathinfo($page_path);
        $page_name = basename($page_path, '.' . $info['extension']);

        // Получить индекс текущего модуля расширения
        $ext_index = $this->getExtensionIndexByName($page_name);

        return $ext_index;
    }

    public function getCurrentSubIndex() {

        $current_ext_index = $this->getCurrentExtIndex();
        $ext = $this->getExtensionByIndex($current_ext_index);

        $sub_index = filter_input(INPUT_GET, "sub");

        if (isset($sub_index)) {
            $sub_index--;

            if ($sub_index >= 0 && $sub_index < $ext->getCount()) {
                return $sub_index;
            }
        }

        return 0;
    }

    /**
     * Метод инициализирует установки текущего модуля расширения и подсистемы.
     */
    protected function initializeExtensions() {
        // Получить текущий индекс
        $ext_index = $this->getCurrentExtIndex();
        // Установить текущий индекс модуля расширения
        if ($ext_index != -1) {
            $this->setCurrentExtIndex($ext_index);
        }

        // Получить индекс текущей подсистемы
        $sub_index = $this->getCurrentSubIndex();

        if ($sub_index != -1) {
            $this->setCurrentSubIndex($sub_index);
        }
    }

    public function __construct() {
        // Инициализировать модули расширения, если они не были загружены ранее
        if (!isset($_SESSION[App::APP_EXTENSIONS])) {
            // Выполнить загрузку модулей
            $ext = new Extensions();
            $this->extensions = $ext;

            // Установить глобальные переменные
            $_SESSION[App::APP_EXTENSIONS] = $ext;
            $_SESSION[App::APP_EXT_CURRENT_INDEX] = 0;
            $_SESSION[App::APP_SUB_CURRENT_INDEX] = 0;
        } else {
            $this->extensions = $_SESSION[App::APP_EXTENSIONS];
            $this->initializeExtensions();
        }
    }

    /**
     * Метод получает модуль расширения по его индексу
     * @param int $index Индекс модуля расширения
     * @return Extention Модуль расширения. Если элемент не найден, вернет -1.
     */
    public function getExtensionByIndex($index) {
        // Получить объект расширения по его индексу
        if (isset($index)) {
            $ext = $this->extensions->items($index);

            return $ext;
        }

        return -1;
    }

    /**
     * Метод получает модуль расширения по его имени
     * @param string $ext_name
     * @return Extention Модуль расширения. Если элемент не найден, вернет -1
     */
    public function getExtensionByName($ext_name) {
        if (isset($ext_name)) {
            $index = 0;
            $stop = FALSE;

            while (!$stop && $index < $this->extensions->getCount()) {
                if ($this->extensions->items($index)->getName() == $ext_name) {
                    $stop = TRUE;

                    return $this->extensions->items($index);
                }
                $index++;
            }
        }

        return \NULL;
    }

    /**
     * Метод получает индекс модуля расширения по его имени
     * @param string $ext_name
     * @return Extention Модуль расширения. Если элемент не найден, вернет -1
     */
    public function getExtensionIndexByName($ext_name) {
        if (isset($ext_name)) {
            $index = 0;
            $stop = FALSE;

            while (!$stop && $index < $this->extensions->getCount()) {
                if ($this->extensions->items($index)->getName() == $ext_name) {
                    $stop = TRUE;

                    return $index;
                }
                $index++;
            }
        }

        return \NULL;
    }

    /**
     * Метод получает подсистему по индексам модуля расширения и подсистемы
     * @param int $ext_index Индекс модуля расширения
     * @param int $sub_index Индекс подсистемы
     * @return Subsystem Подсистема. Если подсистема не найдена, то возвращается -1.
     */
    public function getSubsystemByIndex($ext_index, $sub_index) {
        if (isset($ext_index) && isset($ext_index)) {
            $ext = $this->getExtensionByIndex($ext_index);

            if (isset($ext)) {
                if ($sub_index >= 0 && $sub_index < $ext->getCount()) {
                    $sub = $ext->items($sub_index);

                    return $sub;
                }
            }
        }

        return -1;
    }

    public function getCurrentExtMenu() {
        $ext_index = $this->getCurrentExtIndex();

        if ($ext_index == -1) {
            $ext_index = 0;
        }

        // Получить рендер текущего меню модулей расширения
        $menu = $this->extensions->renderToHTML($ext_index);
        return $menu;
    }

    public function getCurrentSubMenu() {
        $ext_index = $this->getCurrentExtIndex();
        if ($ext_index == -1) {
            $ext_index = 0;
        }

        $sub_index = $this->getCurrentSubIndex();

        if ($sub_index == -1) {
            $sub_index = 0;
        }

        // Получить рендер текущей подсистемы
        $menu = $this->extensions->items($ext_index)->renderToHTML($sub_index);
        return $menu;
    }

}