<?php

class ProductResponse {

    private $products;
    private $total;

    public function __construct()
    {
        $this->products = [];
        $this->total = 0.00;
    }

    /**
    * Add $product of type Product to the products array
    */
    public function addProduct(Product $product)
    {
        $this->products[] = $product;
    }

    public function getProducts($as_array = false)
    {
        // If $as_array is true, call Product->toArray() method
        if ($as_array) {
            return array_map(function($p) {
                return $p->toArray();
            }, $this->products);
        }

        return $this->products;
    }

    /**
    * Calculate and return $total
    */
    public function getTotal()
    {
        $total = 0.00;
        foreach ($this->products as $p) {
            // Remove £ from unit_price
            $unit_price = str_replace('£', '', $p->unit_price);
            $total += (float)$unit_price;
        }

        return round($total, 2);
    }

    /**
    * Return JSON formatted string
    */
    public function toJson()
    {
        return json_encode([
                'results'   => $this->getProducts(true),
                'total'     => $this->getTotal(),
        ]);
    }


}
