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
 * Checkout Multiple Addresses tests with different product types
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CheckoutMultipleAddresses_WithRegistration_WithProductsTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Login to backend</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Checkout with multiple addresses with each product types with all options</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created;</p>
     * <p>Steps:</p>
     * <p>1. Open product page;</p>
     * <p>2. Add product to Shopping Cart;</p>
     * <p>3. Click "Checkout with Multiple Addresses";</p>
     * <p>4. Select Checkout Method with Registering;</p>
     * <p>5. Fill in Personal/Address/Login info;</p>
     * <p>6. Check success registration message;</p>
     * <p>7. Select Shipping Addresses and click "Continue to Shipping Information";</p>
     * <p>8. Fill in Shipping Information tab and click "Continue to Billing Information";</p>
     * <p>9. Fill in Billing Information tab and click "Continue to Review Your Order";</p>
     * <p>10. Verify information into "Order Review" tab;</p>
     * <p>11. Place order;</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful;</p>
     *
     * @dataProvider productData
     * @test
     */
    public function multiCheckoutAllOptionsRegister($productDataSet, $productType)
    {

    }

    public function productData()
    {
        return array(
//            array('dynamic_bundle_visible', 'bundle'),
//            array('fixed_bundle_visible', 'bundle'),
//            array('simple_product_visible'),
//            array('simple_product'),
//            array('virtual_product_visible', 'virtual'),
//            array('virtual_product', 'virtual'),
//            array('downloadable_product_visible', 'downloadable'),
//            array('configurable_product_visible', 'configurable'),
//            array('grouped_product_visible', 'grouped')
        );
    }
}
