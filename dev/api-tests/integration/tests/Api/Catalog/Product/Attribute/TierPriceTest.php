<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        $productData = require dirname(__FILE__) . '/../../_fixtures/ProductData.php';
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
