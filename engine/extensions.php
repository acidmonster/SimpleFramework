<?php
include_once("utils.php");

class Extensions extends BaseList {

    /**
     * Имя GET-параметра, в котором передается имя подсистемы
     */
    const SUBSYSTEM_PROPERTY_NAME = 'sub';

    /**
     * Конструктор класса. Инициализирует все модули расширения
     */

    function __construct() {
        // Вызвать родительский конструктор
        parent::__construct();
        $logger = new Logger(App::APP_LOG_FILE_PATH);
        $XMLConfig = new XMLReader();
        $XMLConfig->open(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT')."".app::APP_EXT_CONFIG_PATH);

        while($XMLConfig->read()) {
            if(($XMLConfig->nodeType == XMLReader::ELEMENT) && ($XMLConfig->name == "extensions")) {
                while($XMLConfig->read()) {
                    if(($XMLConfig->nodeType == XMLReader::ELEMENT) && ($XMLConfig->name == "extension")) {

                        // Прочитать атрибуты модуля расширения
                        $ext_name   = $XMLConfig->getAttribute("name");
                        $ext_title  = $XMLConfig->getAttribute("title");
                        $ext_icon   = $XMLConfig->getAttribute("icon");
                        $ext_state = $XMLConfig->getAttribute("state");
                        
                        //Если расширение отключено, то не создавать объект
                        if($ext_state == "enabled"){

                            // Создать экземпляр класса модуля расширения
                            $ext = new Extention($ext_name, $ext_title, $ext_icon, $ext_state);                                
                            $this->add($ext);

                            while($XMLConfig->read() && $XMLConfig->name != 'extension') {
                                if (($XMLConfig->nodeType == XMLReader::ELEMENT) && ($XMLConfig->name == "subsystems")) {
                                    while ($XMLConfig->read() && $XMLConfig->name != "subsystems") {
                                        if (($XMLConfig->nodeType == XMLReader::ELEMENT) && ($XMLConfig->name == "subsystem")) {
                                            // Прочитать атрибуты подсистемы
                                            $sys_name   = $XMLConfig->getAttribute("name");
                                            $sys_title  = $XMLConfig->getAttribute("title");
                                            $sys_icon   = $XMLConfig->getAttribute("icon");

                                            // Создать экземпляр класса подсистемы
                                            $sys = new Subsystem($sys_name, $sys_title, $sys_icon, $ext);
                                            $this->items($this->getCount() - 1)->add($sys);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Метод получает индекс модуля расширения по его имени
     * @param string $ext_name Наименование модуля расширения
     * @return int Индекс модуля расширения. Если элемент не найден, возвращается -1.
     */
    public function getExtensionIndexByName($ext_name) {
        if (isset($ext_name)){
            $index = 0;
            $stop = FALSE;
            
            while (!$stop && $index < $this->getCount()) {
                if ($this->items($index)->getName() == $ext_name){
                    $stop = TRUE;
                    
                    return $index;
                }
                $index++;
            }
        }
        
        return -1;
    }


    /**
     * Метод устанавливает текущий индекс подсисемы, согласно GET-параметру sub
     */
    public function setCurrentSub(){
        // Получить значение параметра подсистемы
        $sub = filter_input(INPUT_GET, Extensions::SUBSYSTEM_PROPERTY_NAME);
        
        if (isset($sub)) {
            if ($sub > 0 && $sub <= $this->getCount()){
                /*
                 * Записываем индекс с вычетом единицы, 
                 * так как счет элементов списка начинается с 0,
                 * а в строке URL передаем со значением +1.
                 */
                $_SESSION[App::APP_SUB_CURRENT_INDEX] = $sub - 1;
            }
        }
    }


    /**
     * Метод формирует навигационное меню модулей расширений в виде HTML-кода.
     * @return string
     */
    public function renderToHTML($index = 0) {
        $html = '';
        $style = '';
        
        for ($i = 0; $i < $this->getCount(); $i++) {
            
            if($i == $index) {
                $active = ' ext-menu-active-item';
                
                if($i != 0){
                    $style = 'style="border-top: 1px solid #d0ae47"';
                }
                else{
                    $style = '';
                }
            }
            else {
                $active = '';
            } 
            
            $extension = $this->items($i);
            
            $html = $html.'<div class="ext-menu-item'.$active.'" id="'.$extension->getName().'" '.$style.' onMouseDown="loadPage(\''.$extension->getName().'.php\')">'
                            . '<div class="ext-menu-icon" style="background-image: url(..'.$extension->getIcon().')"></div>'
                            . '<div class="ext-menu-label">'.$extension->getTitle().'</div>'
                        . '</div>';
            
        }
        return $html;
    }
}

class Extention extends BaseList {
    protected $name;
    protected $title;
    protected $icon;
    protected $state;


    /**
     * Конструктор модуля расширения
     * @param string $name Наименование модуля расширения
     * @param string $title Заголовок модуля расширения
     * @param string $icon Иконка модуля расширения
     * @param string $state Свойство определяет включено расширение или нет. Доступные значение enabled, disabled.
     */
    function __construct($name, $title, $icon, $state = 'enabled') {
        // Вызвать родительский конструктор
        parent::__construct();
        
        $this->name   = $name;
        $this->title  = $title;
        $this->icon   = $icon;
        
        if(in_array($state, array('enabled', 'disabled'))) {
            $this->state = $state;
        } else {
            $this->state = 'disabled';
        }
    }
    
    public function setState($state) {
        if(in_array($state, array('enabled', 'disabled'))) {
            $this->state = $state;
        } else {
            $this->state = 'disabled';
        }
    }
    
    public function __call($method_name, $arguments) {
        $args = preg_split('/(?<=\w)(?=[A-Z])/', $method_name);
        $action = array_shift($args);
        $property_name = strtolower(implode('_', $args));
        
        switch ($action)
        {
            case 'get':
                return isset($this->$property_name) ? $this->$property_name : null;
 
            case 'set':
                $this->$property_name = $arguments[0];
                return $this;
        }
    }
    
    /**
     * Метод формирует навигационное меню модулей расширений в виде HTML-кода.
     * @param integer $index Индекс элемента, который будет отображен активным
     * @return string
     */
    public function renderToHTML($index = 0) {
        $html = '';
        
        for ($i = 0; $i < $this->getCount(); $i++) {
            $style = '';
            
            $sub = $this->items($i);
            
            if($i == $index) {
                $active = ' sub-menu-active-item';
                
                if($i != 0) {
                    $style = 'style="border-top: 1px solid #d0ae47"';
                }
                else{
                    $style = '';
                }
            } 
            else{
                $active = '';
            }
            
            $link_path = App::APP_EXT_PATH."/".$sub->getParent()->getName().".php?".Extensions::SUBSYSTEM_PROPERTY_NAME."=".($i + 1);
            $html = $html.'<div class="sub-menu-item'.$active.'" id="'.$sub->getName().'" '.$style.' onclick="setPage(\''.$link_path.'\')">'
                            . '<div class="sub-menu-icon" style="background-image: url(..'.$sub->getIcon().')"></div>'
                            . '<div class="sub-menu-label">'.$sub->getTitle().'</div>'
                        . '</div>';
        }
        return $html;
    }
}

class Subsystem {
    protected $name;
    protected $title;
    protected $icon;
    protected $parent;


    /**
     * Конструктор подсистемы
     * @param string $name Наименование подсистемы
     * @param string $title Заголовок подсистемы
     * @param string $icon Путь до файла-изображения
     * @param Extention $parent Родительский элемент
     */
    function __construct($name, $title, $icon, $parent) {
        $this->name   = $name;
        $this->title  = $title;
        $this->icon   = $icon;
        
        if(is_object($parent) && $parent instanceof Extention) {
            $this->parent = $parent;
        } else {          
            throw new Exception("Некорректный тип родительского элемента");
        }
    }
    
    public function setParent($parent) {
        if(is_object($parent) && $parent instanceof Extention) {
            $this->parent = $parent;
        } else {          
            throw new Exception("Некорректный тип родительского элемента");
        }
    }


    public function __call($method_name, $arguments) {
        $args = preg_split('/(?<=\w)(?=[A-Z])/', $method_name);
        $action = array_shift($args);
        $property_name = strtolower(implode('_', $args));
        
        switch ($action)
        {
            case 'get':
                return isset($this->$property_name) ? $this->$property_name : null;
 
            case 'set':
                $this->$property_name = $arguments[0];
                return $this;
        }
    }
}

?>