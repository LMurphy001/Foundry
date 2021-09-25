<?php
class Fruit {
    public $name;
    public $color;
    public $price;
    static $market = "acme";
    function __construct($name,$color,$price) {
        $this->name = $name;
        $this->color = $color;
        $this->price = $price;
    }
}
