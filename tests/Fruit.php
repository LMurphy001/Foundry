<?php
class Fruit {
    public string $id;
    public string $name;
    public string $color;
    public float $price;
    function __construct(string $name, string $color, float $price) {
        $this->name = $name;
        $this->color = $color;
        $this->price = $price;
        $this->id = strval (hash('sha256', $name.$color.strval($price)));
    }
}
