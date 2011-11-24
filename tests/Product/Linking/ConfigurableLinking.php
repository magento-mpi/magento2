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
class Product_Linking_ConfigurableLinkingTest extends Mage_Selenium_TestCase
{

    protected function assertPreConditions()
    {}

    /**
     * <p>Review related products on frontend.</p>
     * <p>Preconditions:</p>
     * <p>Create All Types of products (in stock) and realize next test for all of them;</p>
     * <p>Steps:</p>
     * <p>1. Create 1 configurable product in stock; Attach all types of products to the first one as related products</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page;</p>
     * <p>4. Validate prices for related products in "related products block";</p>
     * <p>Expected result:</p>
     * <p>Products are created, The configurable product contains block with related products; Prices for related products are correct</p>
     *
     * @test
     */
    public function relatedInStock()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Review related products on frontend.</p>
     * <p>Preconditions:</p>
     * <p>Create All Types of products (out of stock) and realize next test for all of them;</p>
     * <p>Steps:</p>
     * <p>1. Create 1 configurable product in stock; Attach all types of products to the first one as related products</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page for the first product;</p>
     * <p>4. Check if the first product contains any related products;</p>
     * <p>Expected result:</p>
     * <p>Products are created, The configurable product does not contains any related products;</p>
     *
     * @test
     */
    public function relatedOutOfStock()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Review Cross-sell products on frontend.</p>
     * <p>Preconditions:</p>
     * <p>Create All Types of products (in stock) and realize next test for all of them;</p>
     * <p>Steps:</p>
     * <p>1. Create 1 configurable product in stock;  Attach all types of products to the first one as cross-sell product</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page;</p>
     * <p>4. Add product to shopping cart;</p>
     * <p>5. Validate prices for cross-sell products in "cross-sell products block" in shopping cart;</p>
     * <p>Expected result:</p>
     * <p>Products are created, The configurable product contains block with cross-sell products; Prices for cross-sell products are correct</p>
     *
     * @test
     */
    public function crossSellsInStock()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Review Cross-sell products on frontend.</p>
     * <p>Preconditions:</p>
     * <p>Create All Types of products (out of stock) and realize next test for all of them;</p>
     * <p>Steps:</p>
     * <p>1. Create 1 configurable products in stock; Attach all types of products to the first one as cross-sell product</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page;</p>
     * <p>4. Add product to shopping cart;</p>
     * <p>5. Validate that shopping cart page with the added product does not contains any cross-sell products;</p>
     * <p>Expected result:</p>
     * <p>Products are created, The configurable product in the shopping cart does not contain the cross-sell products</p>
     *
     * @test
     */
    public function crossSellsOutOfStock()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Review Up-sell products on frontend.</p>
     * <p>Preconditions:</p>
     * <p>Create All Types of products (in stock) and realize next test for all of them;</p>
     * <p>Steps:</p>
     * <p>1. Create 1 configurable product in stock; Attach all types of products to the first one as up-sell products</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page;</p>
     * <p>4. Validate prices for up-sell products in "up-sell products block";</p>
     * <p>Expected result:</p>
     * <p>Products are created, The configurable product contains block with up-sell products; Prices for up-sell products are correct</p>
     *
     * @test
     */
    public function upSellsInStock()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Review Up-sell products on frontend.</p>
     * <p>Preconditions:</p>
     * <p>Create All Types of products (out of stock) and realize next test for all of them;</p>
     * <p>Steps:</p>
     * <p>1. Create 1 configurable product in stock; Attach all types of products to the first one as up-sell products</p>
     * <p>2. Navigate to frontend;</p>
     * <p>3. Open product details page;</p>
     * <p>4. Validate that product details page for the first product does not contain up-sell block with the products;</p>
     * <p>Expected result:</p>
     * <p>Products are created, The configurable product details page does not contain any up-sell product</p>
     *
     * @test
     */
    public function upSellsOutOfStock()
    {
        $this->markTestIncomplete('@TODO');
    }
}

