<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Api_Catalog_Product_Attribute_TierPriceTest extends Magento_Test_Webservice
{
    /**
     * Set up product fixture
     *
     * @return void
     */
    protected function setUp()
    {
        $productData = require realpath(dirname(__FILE__) . '/../_fixture/ProductData.php');
        $product     = new Mage_Catalog_Model_Product;

        $product->setData($productData['create_full_fledged']);
        $product->save();

        $this->setFixture('product', $product);

        parent::setUp();
    }

    /**
     * Delete product fixture
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->deleteFixture('product', true);
        parent::tearDown();
    }

    /**
     * Test product tier price attribute update
     *
     * @return void
     */
    public function testUpdate()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product');

        $result = $this->call('product_tier_price.update', array(
            'productId' => $product->getId(),
            'tierPrices' => array(
                array(
                    'customer_group_id' => Mage_Customer_Model_Group::CUST_GROUP_ALL,
                    'qty' => 3,
                    'price' => 0.88,
                ),
                array(
                    'customer_group_id' => Mage_Customer_Model_Group::CUST_GROUP_ALL,
                    'qty' => 5,
                    'price' => 0.77,
                )
            ),
        ));

        $this->assertTrue((bool) $result, 'Product tier price attribute update API failed');
        // Reload product to check tier prices were applied
        $product->load($product->getId());
        $this->assertEquals($product->getTierPrice(3), 0.88, 'Product tier price (3) attribute update was not applied');
        $this->assertEquals($product->getTierPrice(5), 0.77, 'Product tier price (5) attribute update was not applied');
    }
}
