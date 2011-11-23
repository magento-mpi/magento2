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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test for related, up-sell and cross-sell products.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Product_LinkingTest extends Mage_Selenium_TestCase
{

    protected function assertPreConditions()
    {}

    /**
     * <p>Review related products on frontend.</p>
     * <p>Steps:</p>
     * <p>1. Create 2 simple products in stock; Attach one to another as related product</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page;</p>
     * <p>4. Validate prices for related product in "related products block";</p>
     * <p>Expected result:</p>
     * <p>Products are created, One product contains block with related product; Price for related product is correct</p>
     *
     * @test
     */
    public function relatedInStock()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Review related products on frontend.</p>
     * <p>Steps:</p>
     * <p>1. Create 1 simple product in stock and another simple product out of stock; Attach the second one to the first as related product</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page for the first product;</p>
     * <p>4. Check if the first product contains any related products;</p>
     * <p>Expected result:</p>
     * <p>Products are created, The first product does not contains any related products;</p>
     *
     * @test
     */
    public function relatedOutOfStock()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Review Cross-sell products on frontend.</p>
     * <p>Steps:</p>
     * <p>1. Create 2 simple products in stock; Attach one to another as cross-sell product</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page;</p>
     * <p>4. Add product to shopping cart;</p>
     * <p>5. Validate prices for cross-sell product in "cross-sell products block" in shopping cart;</p>
     * <p>Expected result:</p>
     * <p>Products are created, One product contains block with cross-sell product; Price for cross-sell product is correct</p>
     *
     * @test
     */
    public function crossSellsInStock()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Review Cross-sell products on frontend.</p>
     * <p>Steps:</p>
     * <p>1. Create 1 simple products in stock and another one out of stock; Attach the second one to the first one as cross-sell product</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page;</p>
     * <p>4. Add product to shopping cart;</p>
     * <p>5. Validate that shopping cart page with the added product does not contains any cross-sell products;</p>
     * <p>Expected result:</p>
     * <p>Products are created, The first product in the shopping cart does not provided with the cross-sell products block</p>
     *
     * @test
     */
    public function crossSellsOutOfStock()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Review Up-sell products on frontend.</p>
     * <p>Steps:</p>
     * <p>1. Create 2 simple products in stock; Attach one to another as up-sell product</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page;</p>
     * <p>4. Validate prices for up-sell product in "up-sell products block";</p>
     * <p>Expected result:</p>
     * <p>Products are created, One product contains block with up-sell product; Price for up-sell product is correct</p>
     *
     * @test
     */
    public function upSellsInStock()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Review Up-sell products on frontend.</p>
     * <p>Steps:</p>
     * <p>1. Create 1 simple products in stock and another one out of stock; Attach the second one to the first one as up-sell product</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page;</p>
     * <p>4. Validate that product details page for the first product does not contain up-sell block with the second product;</p>
     * <p>Expected result:</p>
     * <p>Products are created, The first products detail page does not contain the up-sell products block with the second product</p>
     *
     * @test
     */
    public function upSellsOutOfStock()
    {
        $this->markTestIncomplete('@TODO');
    }
}
