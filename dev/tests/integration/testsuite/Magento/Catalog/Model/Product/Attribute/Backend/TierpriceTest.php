<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Attribute\Backend;

/**
 * Test class for \Magento\Catalog\Model\Product\Attribute\Backend\Tierprice.
 *
 * @magentoDataFixture Magento/Catalog/_files/product_simple.php
 */
class TierpriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Backend\Tierprice
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product\Attribute\Backend\Tierprice'
        );
        $this->_model->setAttribute(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Eav\Model\Config'
            )->getAttribute(
                'catalog_product',
                'tier_price'
            )
        );
    }

    public function testValidate()
    {
        $product = new \Magento\Framework\Object();
        $product->setTierPrice(
            array(
                array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 2, 'price' => 8),
                array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 5, 'price' => 5)
            )
        );
        $this->assertTrue($this->_model->validate($product));
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     */
    public function testValidateDuplicate()
    {
        $product = new \Magento\Framework\Object();
        $product->setTierPrice(
            array(
                array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 2, 'price' => 8),
                array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 2, 'price' => 8)
            )
        );

        $this->_model->validate($product);
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     */
    public function testValidateDuplicateWebsite()
    {
        $product = new \Magento\Framework\Object();
        $product->setTierPrice(
            array(
                array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 2, 'price' => 8),
                array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 5, 'price' => 5),
                array('website_id' => 1, 'cust_group' => 1, 'price_qty' => 5, 'price' => 5)
            )
        );

        $this->_model->validate($product);
    }

    public function testPreparePriceData()
    {
        $data = array(
            array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 2, 'price' => 8),
            array('website_id' => 0, 'cust_group' => 1, 'price_qty' => 5, 'price' => 5),
            array('website_id' => 1, 'cust_group' => 1, 'price_qty' => 5, 'price' => 5)
        );

        $newData = $this->_model->preparePriceData($data, \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE, 1);
        $this->assertEquals(2, count($newData));
        $this->assertArrayHasKey('1-2', $newData);
        $this->assertArrayHasKey('1-5', $newData);
    }

    public function testAfterLoad()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $product->setId(1);
        $this->_model->afterLoad($product);
        $price = $product->getTierPrice();
        $this->assertNotEmpty($price);
        $this->assertEquals(3, count($price));
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testAfterSave()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $product->load(1);
        $product->unlockAttributes();
        $product->setOrigData();
        $product->setTierPrice(
            array(
                array('website_id' => 0, 'cust_group' => 32000, 'price_qty' => 2, 'price' => 7, 'delete' => true),
                array('website_id' => 0, 'cust_group' => 32000, 'price_qty' => 5, 'price' => 4),
                array('website_id' => 0, 'cust_group' => 32000, 'price_qty' => 10, 'price' => 3),
                array('website_id' => 0, 'cust_group' => 32000, 'price_qty' => 20, 'price' => 2)
            )
        );

        $this->_model->afterSave($product);

        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $product->setId(1);
        $this->_model->afterLoad($product);
        $this->assertEquals(3, count($product->getTierPrice()));
    }

    /**
     * @depends testAfterSave
     */
    public function testAfterSaveEmpty()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\StoreManagerInterface'
        )->setCurrentStore(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Framework\StoreManagerInterface'
            )->getStore(
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            )
        );
        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $product->load(1);
        $product->setOrigData();
        $product->setTierPrice(array());
        $this->_model->afterSave($product);

        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $product->setId(1);
        $this->_model->afterLoad($product);
        $this->assertEmpty($product->getTierPrice());
    }
}
