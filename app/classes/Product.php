<?php

use PHPHtmlParser\Dom as Dom;

class Product {

    private $dom;
    private $parent_dom;
    private $url;
    private $curl;

    public function __construct(Dom $dom = null)
    {
        if (is_null($dom)) {
            $dom = new Dom;
        }

        $this->dom = $dom;
    }

    // public function setDom(Dom $dom)
    // {
    //     $this->dom = $dom;
    // }

    public function getDom()
    {
        return $this->dom;
    }

    /**
    * Parent dom contains the URL to the product page
    */
    public function setParentDom($dom)
    {
        $this->parent_dom = $dom;
    }

    public function loadFromUrl($url, $options = [], Curl $curl = null)
    {
        $this->curl = $curl;

        $this->initDom($url, $options, $curl);
    }

    private function initDom($url, $options = [], Curl $curl = null)
    {
        $this->dom->loadFromUrl($url, $options, $curl);
    }

    /**
    * Get URL for the product page from parent dom
    */
    public function getPageUrl()
    {
        return $this->parent_dom->find('.productInfo a')->getAttribute('href');
    }

    public function getPageSize($unit = 'kb')
    {
        if (!is_null($this->curl)) {
            return $this->curl->getSize($unit);
        }

        return '';
    }

    public function getTitle()
    {
        return $this->dom->find('.productSummary h1')->text();
    }

    /**
    * Get product description from the product page
    */
    public function getDescription()
    {
        // First we try extracting description from the div.productText next to
        // the h3.productDataItemHeader (with text = "Description").
        // If this fails (on some product pages) we get description
        // from the div.longTextItems within
        // div.itemTypeGroupContainer
        $div_description = $this->dom->find('.productDataItemHeader');
        if (count($div_description) > 0) {
            if ($div_description[0]->text() == 'Description') {
                $product_description = $div_description
                    ->nextSibling() // blank
                    ->nextSibling() // div with the "description"
                    ->innerHtml();
            }
        } else { // For some products, description is in .longTextItems
            $div_description = $this->dom->find('.itemTypeGroupContainer .longTextItems');
            if (count($div_description) > 0) {
                $product_description = $div_description->innerHtml();
            }
        }

        // Remove any whitespace and strip any HTML tags from the description field.
        return trim(strip_tags($product_description));
    }

    /**
    * Get Product's unit price
    */
    public function getUnitPrice()
    {
        return preg_replace(
            '/£|\s/', // Replace £ or any whitespace
            '', // with nothing
            $this->dom->find('.productSummary p.pricePerUnit')->text()
        );
    }

    /**
    * Return all the properties of the product in array format
    */
    public function toArray()
    {
        return [
            'title'         => $this->getTitle(),
            'size'          => $this->getPageSize(),
            'unit_price'    => $this->getUnitPrice(),
            'description'   => $this->getDescription(),
        ];
    }
}
