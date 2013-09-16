<?php
/**
 * Product tier price attribute API test.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDbIsolation enabled
 */
class Magento_Catalog_Model_Product_Api_Attribute_TierPriceTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Catalog_Model_Product */
    protected $_product;

    /**
     * Set up product fixture
     */
    protected function setUp()
    {
        $this->markTestSkipped('Api tests were skipped');
        $productData = require realpath(dirname(__FILE__) . '/../_files/ProductData.php');
        $product = Mage::getModel('Magento_Catalog_Model_Product');

        $product->setData($productData['create_full_fledged']);
        $product->save();

        $this->_product = $product;

        parent::setUp();
    }

    /**
     * Test product tier price attribute update
     */
    public function testUpdate()
    {
        $result = Magento_TestFramework_Helper_Api::call(
            $this,
            'catalogProductAttributeTierPriceUpdate',
            array(
                'productId' => $this->_product->getId(),
                'tierPrices' => array(
                    (object)array(
                        'customer_group_id' => Magento_Customer_Model_Group::CUST_GROUP_ALL,
                        'qty' => 3,
                        'price' => 0.88,
                    ),
                    (object)array(
                        'customer_group_id' => Magento_Customer_Model_Group::CUST_GROUP_ALL,
                        'qty' => 5,
                        'price' => 0.77,
                    )
                ),
            )
        );

        $this->assertTrue((bool)$result, 'Product tier price attribute update API failed');
        // Reload product to check tier prices were applied
        $this->_product->load($this->_product->getId());
        $this->assertEquals(
            $this->_product->getTierPrice(3),
            0.88,
            'Product tier price (3) attribute update was not applied'
        );
        $this->assertEquals(
            $this->_product->getTierPrice(5),
            0.77,
            'Product tier price (5) attribute update was not applied'
        );
    }
}
