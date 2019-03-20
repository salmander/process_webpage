<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/config/config.php';

use PHPHtmlParser\Dom;

echo 'Getting data from: '. URL . PHP_EOL;

// Instantiate new DOM for Sainsbury's Ripe Fruits webpage
$dom = new Dom;

// Load and parse Sainsbury's Ripe Fruits webpage
$dom->loadFromUrl(URL, [], new \Curl);

// In order to find all the urls to the product pages, we need to
// find all the divs with the .product class
$products = $dom->find('#productLister .product');

echo "Total products: " . count($products) . PHP_EOL;
$product_response = new ProductResponse();

foreach ($products as $p) {
    // Get the URL to the products page
    $href = $p->find('.productInfo a')->getAttribute('href');

    echo 'Get content for: ' . $href . PHP_EOL;

    // Instantiate new DOM for product page.
    $pp_dom = new Dom();

    // Load and parse product page HTML
    $product_page_curl = new \Curl;
    $pp_dom->loadFromUrl($href, [], $product_page_curl);

    // Instantiate new Product
    $product = new Product;

    // Find and assign title to the Product
    $product->title = $pp_dom->find('.productSummary h1')->text();

    // Assign page size to the Product
    $product->size = $product_page_curl->getSize('kb');

    // Find unit_price then, remove £ and any whitespace
    // And assign it to the Product
    $product->unit_price = preg_replace(
        '/£|\s/', // Replace £ or any whitespace
        '', // with nothing
        $pp_dom->find('.productSummary p.pricePerUnit')->text()
    );

    // First we try extracting description from the div.productText next to
    // the h3.productDataItemHeader (with text = "Description").
    // If this fails (on some product pages) we get description
    // from the div.longTextItems within
    // div.itemTypeGroupContainer
    $div_description = $pp_dom->find('.productDataItemHeader');
    if (count($div_description) > 0) {
        if ($div_description[0]->text() == 'Description') {
            $product->description = $div_description
                ->nextSibling() // blank
                ->nextSibling() // div with the "description"
                ->innerHtml();
        }
    } else { // For some products, description is in .longTextItems
        $div_description = $pp_dom->find('.itemTypeGroupContainer .longTextItems');
        if (count($div_description) > 0) {
            $product->description = $div_description->innerHtml();
        }
    }

    // Remove any whitespace and strip any HTML tags from the description field.
    $product->description = trim(strip_tags($product->description));

    // Add this product to the ProductResponse
    $product_response->addProduct($product);
}

echo $product_response->toJson();
