<?php
require __DIR__ . DIRECTORY_SEPARATOR . 'Fruit.php';

class FruitList {
    public $items;
    function __construct() {
        $this->items = [];
    }
    function add_fruit($name, $color, $price) : string {
        $f = new Fruit($name, $color, $price);
        $this->items[$f->id] = $f;
        return $f->id;
    }
    function print() {
        print_r($this);
    }
}

$list = new FruitList();
$list->add_fruit("apple", "red", 1.29);
$list->add_fruit("peach", "yellow", 0.99);
$list->add_fruit("banana", "red", .89);
$list->add_fruit("orange", "orange", 1.49);
$list->add_fruit("papaya", "orange", 1.99);

$list->print();
