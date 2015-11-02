<?php

class ProductTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    // tests
    public function testProductToArrayMethod()
    {
        $product = new Product();
        $product->title = 'title';
        $product->size = 1024;
        $product->description = 'description';
        $product->unit_price = 1.5;

        $expected = [
            'title'         => 'title',
            'size'          => 1024,
            'description'   => 'description',
            'unit_price'    => 1.5,
        ];

        $this->assertEquals($expected, $product->toArray());
    }
}
