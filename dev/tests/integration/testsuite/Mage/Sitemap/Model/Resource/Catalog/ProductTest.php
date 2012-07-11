<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sitemap
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Sitemap_Model_Resource_Catalog_Product.
 * - test products collection generation for sitemap
 */
class Mage_Sitemap_Model_Resource_Catalog_ProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Sitemap/_files/sitemap_products.php
     */
    public function testGetCollection()
    {
        $model = new Mage_Sitemap_Model_Resource_Catalog_Product();
        $products = $model->getCollection(Mage_Core_Model_App::DISTRO_STORE_ID);

        // Check all expected products were added into collection
        $this->assertCount(2, $products);
        $this->assertArrayHasKey(1, $products);
        $this->assertArrayHasKey(4, $products);

        // Check all expected attributes are present
        foreach ($products as $product) {
            $this->assertNotEmpty($product->getUpdatedAt());
            $this->assertNotEmpty($product->getId());
            $this->assertNotEmpty($product->getName());
            $this->assertNotEmpty($product->getUrl());
        }

        // Check thumbnail attribute
        $this->assertEmpty($products[1]->getThumbnail());
        $this->assertEquals('/m/a/magento_image_sitemap.png', $products[4]->getThumbnail());

        // Check images loading
        $this->assertEmpty($products[1]->getImages());
        $this->assertNotEmpty($products[4]->getImages());
        $this->assertEquals('Simple Images', $products[4]->getImages()->getTitle());
        $this->assertEquals('/m/a/magento_image_sitemap.png', $products[4]->getImages()->getThumbnail());
        $this->assertCount(1, $products[4]->getImages()->getCollection());

        $imagesCollection = $products[4]->getImages()->getCollection();
        $this->assertEquals('catalog/product/m/a/magento_image_sitemap.png', $imagesCollection[0]->getUrl());
        $this->assertEmpty($imagesCollection[0]->getCaption());
    }
}
