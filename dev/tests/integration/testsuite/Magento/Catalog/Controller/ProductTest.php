<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Catalog\Controller\Product.
 */
class Magento_Catalog_Controller_ProductTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    public function assert404NotFound()
    {
        parent::assert404NotFound();
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->assertNull($objectManager->get('Magento\Core\Model\Registry')->registry('current_product'));
    }

    protected function _getProductImageFile()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1);
        $images = $product->getMediaGalleryImages()->getItems();
        $image = reset($images);
        return $image['file'];
    }

    /**
     * @magentoDataFixture Magento/Catalog/controllers/_files/products.php
     */
    public function testViewAction()
    {
        $this->dispatch('catalog/product/view/id/1');
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        /** @var $currentProduct \Magento\Catalog\Model\Product */
        $currentProduct = $objectManager->get('Magento\Core\Model\Registry')->registry('current_product');
        $this->assertInstanceOf('Magento\Catalog\Model\Product', $currentProduct);
        $this->assertEquals(1, $currentProduct->getId());

        $lastViewedProductId = Mage::getSingleton('Magento\Catalog\Model\Session')->getLastViewedProductId();
        $this->assertEquals(1, $lastViewedProductId);

        /* Layout updates */
        $handles = Mage::app()->getLayout()->getUpdate()->getHandles();
        $this->assertContains('catalog_product_view_type_simple', $handles);

        $responseBody = $this->getResponse()->getBody();
        /* Product info */
        $this->assertContains('Simple Product 1 Name', $responseBody);
        $this->assertContains('Simple Product 1 Full Description', $responseBody);
        $this->assertContains('Simple Product 1 Short Description', $responseBody);
        /* Stock info */
        $this->assertContains('$1,234.56', $responseBody);
        $this->assertContains('In stock', $responseBody);
        $this->assertContains('Add to Cart', $responseBody);
        /* Meta info */
        $this->assertContains('<title>Simple Product 1 Meta Title</title>', $responseBody);
        $this->assertSelectCount('meta[name="keywords"][content="Simple Product 1 Meta Keyword"]', 1, $responseBody);
        $this->assertSelectCount(
            'meta[name="description"][content="Simple Product 1 Meta Description"]',
            1,
            $responseBody
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testViewActionConfigurable()
    {
        $this->dispatch('catalog/product/view/id/1');
        $html = $this->getResponse()->getBody();
        $this->assertSelectCount('#product-options-wrapper', 1, $html);
    }

    public function testViewActionNoProductId()
    {
        $this->dispatch('catalog/product/view/id/');

        $this->assert404NotFound();
    }

    public function testViewActionRedirect()
    {
        $this->dispatch('catalog/product/view/?store=default');

        $this->assertRedirect();
    }

    /**
     * @magentoDataFixture Magento/Catalog/controllers/_files/products.php
     */
    public function testGalleryAction()
    {
        $this->dispatch('catalog/product/gallery/id/1');

        $this->assertContains('http://localhost/pub/media/catalog/product/', $this->getResponse()->getBody());
        $this->assertContains($this->_getProductImageFile(), $this->getResponse()->getBody());
    }

    public function testGalleryActionRedirect()
    {
        $this->dispatch('catalog/product/gallery/?store=default');

        $this->assertRedirect();
    }

    public function testGalleryActionNoProduct()
    {
        $this->dispatch('catalog/product/gallery/id/');

        $this->assert404NotFound();
    }

    /**
     * @magentoDataFixture Magento/Catalog/controllers/_files/products.php
     */
    public function testImageAction()
    {
        $this->markTestSkipped("All logic has been cut to avoid possible malicious usage of the method");
        ob_start();
        /* Preceding slash in URL is required in this case */
        $this->dispatch('/catalog/product/image' . $this->_getProductImageFile());
        $imageContent = ob_get_clean();
        /**
         * Check against PNG file signature.
         * @link http://www.libpng.org/pub/png/spec/1.2/PNG-Rationale.html#R.PNG-file-signature
         */
        $this->assertStringStartsWith(sprintf("%cPNG\r\n%c\n", 137, 26), $imageContent);
    }

    public function testImageActionNoImage()
    {
        $this->dispatch('catalog/product/image/');

        $this->assert404NotFound();
    }
}
