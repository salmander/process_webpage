<?php

use PHPHtmlParser\Dom as Dom;

class ProductListPage {

    private $dom;

    public function __construct(Dom $dom)
    {
        $this->dom = $dom;
    }

    public function setDom(Dom $dom)
    {
        $this->dom = $dom;
    }

    public function getDom()
    {
        return $this->dom;
    }

    /**
    * Get all the divs with the .product class in #productsContainer
    */
    public function getProducts()
    {
        $products = $this->dom->find('#productsContainer .product');
        if (count($products)) {
            return $products;
        }

        return [];
    }
}
