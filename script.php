<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/config/config.php';

use PHPHtmlParser\Dom;

echo 'Getting data from: '. URL . PHP_EOL;

// Instantiate new DOM for Sainsbury's Ripe Fruits webpage
$dom = new Dom;

// Load and parse Sainsbury's Ripe Fruits webpage
$dom->loadFromUrl(URL, [], new \Curl);

// Instantiate ProductListPage
$product_list = new ProductListPage($dom);

// Get all products
$products = $product_list->getProducts();

echo 'Total products on Product List page: ' . count($products) . PHP_EOL;

// Product Response will contains all the products in the expected format
// with total price
$product_response = new ProductResponse();

// Go through each product
foreach ($products as $p) {
    // Product object will hold all the product related information
    $product = new Product();

    // Need to set the parent dom to obtain product page URL
    $product->setParentDom($p);

    // Get the URL to the product page
    $href = $product->getPageUrl();

    echo 'Get content for: ' . $href . PHP_EOL;

    // Load the product page by URL (also provide our extended Curl class)
    $product->loadFromUrl($href, [], new \Curl);

    // Add this product to the ProductResponse
    $product_response->addProduct($product);
}

echo $product_response->toJson();
