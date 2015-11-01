<?php

class Product {
    
    private $title;
    private $size;
    private $unit_price;
    private $description;

    public function __construct()
    {

    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    /**
    * Return all the properties in array format
    */
    public function toArray()
    {
        $array = [];
        foreach ($this as $k => $v) {
            $array[$k] = $v;
        }

        return $array;
    }
}
