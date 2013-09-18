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

class Magento_Catalog_Helper_ProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Catalog\Helper\Product');
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testGetProductUrl()
    {
        $expectedUrl = 'http://localhost/index.php/simple-product.html';

        // product as object
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1);
        $this->assertEquals($expectedUrl, $this->_helper->getProductUrl($product));

        // product as ID
        $this->assertEquals($expectedUrl, $this->_helper->getProductUrl(1));
    }

    public function testGetPrice()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->setPrice(49.95);
        $this->assertEquals(49.95, $this->_helper->getPrice($product));
    }

    public function testGetFinalPrice()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->setFinalPrice(49.95);
        $this->assertEquals(49.95, $this->_helper->getFinalPrice($product));
    }

    public function testGetImageUrl()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $this->assertStringEndsWith('placeholder/image.jpg', $this->_helper->getImageUrl($product));

        $product->setImage('test_image.png');
        $this->assertStringEndsWith('/test_image.png', $this->_helper->getImageUrl($product));
    }

    public function testGetSmallImageUrl()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $this->assertStringEndsWith('placeholder/small_image.jpg', $this->_helper->getSmallImageUrl($product));

        $product->setSmallImage('test_image.png');
        $this->assertStringEndsWith('/test_image.png', $this->_helper->getSmallImageUrl($product));
    }

    public function testGetThumbnailUrl()
    {
        $this->assertEmpty($this->_helper->getThumbnailUrl(Mage::getModel('Magento\Catalog\Model\Product')));
    }

    public function testGetEmailToFriendUrl()
    {
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->setId(100);
        $category = Mage::getModel('Magento\Catalog\Model\Category');
        $category->setId(10);
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_category', $category);

        try {
            $this->assertStringEndsWith(
                'sendfriend/product/send/id/100/cat_id/10/', $this->_helper->getEmailToFriendUrl($product)
            );
            $objectManager->get('Magento\Core\Model\Registry')->unregister('current_category');
        } catch (Exception $e) {
            $objectManager->get('Magento\Core\Model\Registry')->unregister('current_category');
            throw $e;
        }
    }

    public function testGetStatuses()
    {
        $this->assertEquals(array(), $this->_helper->getStatuses());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testCanShow()
    {
        // non-visible or disabled
        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $this->assertFalse($this->_helper->canShow($product));

        // enabled and visible
        $product->setId(1);
        $product->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED);
        $product->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $this->assertTrue($this->_helper->canShow($product));

        $this->assertTrue($this->_helper->canShow(1));
    }

    public function testGetProductUrlSuffix()
    {
        $this->assertEquals('.html', $this->_helper->getProductUrlSuffix());
    }

    public function testCanUseCanonicalTagDefault()
    {
        $this->assertEquals('0', $this->_helper->canUseCanonicalTag());
    }

    /**
     * @magentoConfigFixture current_store catalog/seo/product_canonical_tag 1
     */
    public function testCanUseCanonicalTag()
    {
        $this->assertEquals(1, $this->_helper->canUseCanonicalTag());
    }

    public function testGetAttributeInputTypes()
    {
        $types = $this->_helper->getAttributeInputTypes();
        $this->assertArrayHasKey('multiselect', $types);
        $this->assertArrayHasKey('boolean', $types);
        foreach ($types as $type) {
            $this->assertInternalType('array', $type);
            $this->assertNotEmpty($type);
        }

        $this->assertNotEmpty($this->_helper->getAttributeInputTypes('multiselect'));
        $this->assertNotEmpty($this->_helper->getAttributeInputTypes('boolean'));
    }

    public function testGetAttributeBackendModelByInputType()
    {
        $this->assertNotEmpty($this->_helper->getAttributeBackendModelByInputType('multiselect'));
        $this->assertNull($this->_helper->getAttributeBackendModelByInputType('boolean'));
    }

    public function testGetAttributeSourceModelByInputType()
    {
        $this->assertNotEmpty($this->_helper->getAttributeSourceModelByInputType('boolean'));
        $this->assertNull($this->_helper->getAttributeSourceModelByInputType('multiselect'));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     * @magentoAppIsolation enabled
     */
    public function testInitProduct()
    {
        Mage::getSingleton('Magento\Catalog\Model\Session')->setLastVisitedCategoryId(2);
        $this->_helper->initProduct(1, 'view');
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $this->assertInstanceOf(
            'Magento\Catalog\Model\Product',
            $objectManager->get('Magento\Core\Model\Registry')->registry('current_product')
        );
        $this->assertInstanceOf(
            'Magento\Catalog\Model\Category',
            $objectManager->get('Magento\Core\Model\Registry')->registry('current_category')
        );
    }

    public function testPrepareProductOptions()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $buyRequest = new \Magento\Object(array('qty' => 100, 'options' => array('option' => 'value')));
        $this->_helper->prepareProductOptions($product, $buyRequest);
        $result = $product->getPreconfiguredValues();
        $this->assertInstanceOf('Magento\Object', $result);
        $this->assertEquals(100, $result->getQty());
        $this->assertEquals(array('option' => 'value'), $result->getOptions());
    }
}
