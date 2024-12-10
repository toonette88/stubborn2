<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductInitialization()
    {
        // Créez une instance de Product
        $product = new Product();

        // Vérifiez que is_featured est bien initialisé à true
        $this->assertTrue($product->isFeatured());
    }

    public function testSetAndGetName()
    {
        $product = new Product();

        // Teste le setter et le getter pour 'name'
        $product->setName('Test Product');
        $this->assertEquals('Test Product', $product->getName());
    }

    public function testSetAndGetDescription()
    {
        $product = new Product();

        // Teste le setter et le getter pour 'description'
        $product->setDescription('This is a test description.');
        $this->assertEquals('This is a test description.', $product->getDescription());
    }

    public function testSetAndGetPrice()
    {
        $product = new Product();

        // Teste le setter et le getter pour 'price'
        $product->setPrice(19.99);
        $this->assertEquals(19.99, $product->getPrice());
    }

    public function testSetAndGetImage()
    {
        $product = new Product();

        // Teste le setter et le getter pour 'image'
        $product->setImage('image.jpg');
        $this->assertEquals('image.jpg', $product->getImage());
    }

    public function testSetAndGetStockXS()
    {
        $product = new Product();

        // Teste le setter et le getter pour 'stockXS'
        $product->setStockXS(10);
        $this->assertEquals(10, $product->getStockXS());
    }

    public function testSetAndGetStockS()
    {
        $product = new Product();

        // Teste le setter et le getter pour 'stockS'
        $product->setStockS(20);
        $this->assertEquals(20, $product->getStockS());
    }

    public function testSetAndGetStockM()
    {
        $product = new Product();

        // Teste le setter et le getter pour 'stockM'
        $product->setStockM(30);
        $this->assertEquals(30, $product->getStockM());
    }

    public function testSetAndGetStockL()
    {
        $product = new Product();

        // Teste le setter et le getter pour 'stockL'
        $product->setStockL(40);
        $this->assertEquals(40, $product->getStockL());
    }

    public function testSetAndGetStockXL()
    {
        $product = new Product();

        // Teste le setter et le getter pour 'stockXL'
        $product->setStockXL(50);
        $this->assertEquals(50, $product->getStockXL());
    }

    public function testSetAndGetFeatured()
    {
        $product = new Product();

        // Teste le setter et le getter pour 'is_featured'
        $product->setIsFeatured(false);
        $this->assertFalse($product->isFeatured());

        $product->setIsFeatured(true);
        $this->assertTrue($product->isFeatured());
    }
}
