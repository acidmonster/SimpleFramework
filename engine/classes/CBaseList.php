<?php

/**
 * Класс простого списка
 */
class CBaseList {

    /**
     * Массив элементов списка
     * @var Array
     */
    protected $items = [];

    /**
     * Число элементов в списке
     * @var Integer
     */
    protected $count;

    function __construct() {
        $this->count = 0;
    }

    /**
     * Метод добавляет новый элемент в список
     * @param variant $value
     */
    public function add($value) {
        // Add new item to array
        if ($value) {
            $this->items[$this->count] = $value;
            $this->count++;
        }
    }

    /**
     * Удаляет элемент списка по его индексу
     * @param integer $index
     */
    public function delete($index) {
        if ($index) {
            if ($index >= 0 && $index < $this->count) {
                unset($this->items[$index]);
                $this->items = array_values($this->items);
            }
        }
    }

    /**
     * Получает элемент списка по его индексу
     * @param integer $index
     * @return variant
     */
    public function items($index) {
        if (!is_int($index)) {
            $index = 0;
        }

        if ($index >= 0 && $index < $this->count) {
            return $this->items[$index];
        }

        return "";
    }

    /**
     * Метод возвращает количество элементов списка
     * @return integer
     */
    public function getItemsCount() {
        return $this->count;
    }

}