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
 * Test class for \Magento\Catalog\Model\Product\Attribute\Backend\Tierprice.
 *
 * @magentoDataFixture Magento/Catalog/_files/product_simple.php
 */
namespace Magento\Catalog\Model\Product\Attribute\Backend;

class TierpriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Backend\Tierprice
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Mage::getModel('Magento\Catalog\Model\Product\Attribute\Backend\Tierprice');
        $this->_model->setAttribute(
            \Mage::getSingleton('Magento\Eav\Model\Config')->getAttribute('catalog_product', 'tier_price')
        );
    }

    public function testValidate()
    {
        $product = new \Magento\Object();
        $product->setTierPrice(
            array(
                array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 2, 'price' => 8,),
                array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 5, 'price' => 5,),
            )
        );
        $this->assertTrue($this->_model->validate($product));
    }

    /**
     * @expectedException \Magento\Core\Exception
     */
    public function testValidateDuplicate()
    {
        $product = new \Magento\Object();
        $product->setTierPrice(
            array(
                array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 2, 'price' => 8,),
                array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 2, 'price' => 8,),
            )
        );

        $this->_model->validate($product);
    }

    /**
     * @expectedException \Magento\Core\Exception
     */
    public function testValidateDuplicateWebsite()
    {
        $product = new \Magento\Object();
        $product->setTierPrice(
            array(
                array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 2, 'price' => 8,),
                array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 5, 'price' => 5,),
                array('website_id' => 1, 'cust_group' => 1, 'price_qty' => 5, 'price' => 5,),
            )
        );

        $this->_model->validate($product);
    }

    public function testPreparePriceData()
    {
        $data = array(
            array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 2, 'price' => 8,),
            array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 5, 'price' => 5,),
            array('website_id' => 1, 'cust_group' => 1, 'price_qty' => 5, 'price' => 5,),
        );

        $newData = $this->_model->preparePriceData($data, \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE, 1);
        $this->assertEquals(2, count($newData));
        $this->assertArrayHasKey('1-2', $newData);
        $this->assertArrayHasKey('1-5', $newData);
    }

    public function testAfterLoad()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Mage::getModel('Magento\Catalog\Model\Product');
        $product->setId(1);
        $this->_model->afterLoad($product);
        $price = $product->getTierPrice();
        $this->assertNotEmpty($price);
        $this->assertEquals(2, count($price));
    }

    public function testAfterSave()
    {
        \Mage::app()->setCurrentStore(\Mage::app()->getStore(\Magento\Core\Model\AppInterface::ADMIN_STORE_ID));
        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1);
        $product->setOrigData();
        $product->setTierPrice(
            array(
                array('website_id' => 0, 'cust_group' => 32000, 'price_qty' => 2, 'price' => 7, 'delete' => true),
                array('website_id' => 0, 'cust_group' => 32000, 'price_qty' => 5, 'price' => 4,),
                array('website_id' => 0, 'cust_group' => 32000, 'price_qty' => 10,'price' => 3,),
                array('website_id' => 0, 'cust_group' => 32000, 'price_qty' => 20,'price' => 2,),
            )
        );

        $this->_model->afterSave($product);

        $product = \Mage::getModel('Magento\Catalog\Model\Product');
        $product->setId(1);
        $this->_model->afterLoad($product);
        $this->assertEquals(3, count($product->getTierPrice()));
    }

    /**
     * @depends testAfterSave
     */
    public function testAfterSaveEmpty()
    {
        \Mage::app()->setCurrentStore(\Mage::app()->getStore(\Magento\Core\Model\AppInterface::ADMIN_STORE_ID));
        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1);
        $product->setOrigData();
        $product->setTierPrice(array());
        $this->_model->afterSave($product);

        $product = \Mage::getModel('Magento\Catalog\Model\Product');
        $product->setId(1);
        $this->_model->afterLoad($product);
        $this->assertEmpty($product->getTierPrice());
    }

}
