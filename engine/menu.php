<?php

/**
 * Description of menu
 *
 * @author Kalashnikov A.P.
 */

include_once './utils.php';

class menuItem {
    public $title;
    public $image;
    public $page;
    
    protected $state;
    
    public function __construct($state, $title, $image, $page) {
        $this->state = $state;
        $this->title = $title;
        $this->image = $image;
        $this->page  = $page;
    }
}

class menu {
    public $items;

    protected $item_count;
    protected $current_item;


    public function __construct() {
        // Set Item Counter
        $this->item_count = 0;
        $this->setCurrentItem();
        
        // TODO: Get the menu items
    }
    
    public function addItem($state, $title, $image, $page) {
        if (!isset($state) || 
            !isset($title) ||
            !isset($image) ||
            !isset($page))
        {
            die('Variables of the Menu items must be define.');
        }
        
        $menu_item = new menuItem($state, $title, $image, $page);
        $this->items[$this->item_count] = $menu_item;
        $this->item_count++;
    }
    
    public function draw() {
        // Draw Menu items
        $output = '<div class="menu_canvas"><input type="hidden" id="current_item" value="'. $this->current_item .'" />';
        for($i = 0; $i < $this->item_count; $i++){
            $menu_item = $this->items[$i];
            $class_name = "menu_simple_item";
            
            if($menu_item->state) {
               $class_name = "menu_checked_item";
            }
            
            $output .= '<div class="' . $class_name . '" id="menu_item_'. $i .'" onClick="menuButtonClick('. $this->current_item .')"></div>';
        }
        
        $output .= '</div>';
    }
    
    public function setCurrentItem() {
        
    }   
}