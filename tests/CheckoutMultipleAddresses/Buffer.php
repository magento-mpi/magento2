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
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CheckoutMultipleAddresses_Buffer extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
    }

    /**
     * @test
     */
    public function some()
    {
        $this->logoutCustomer();
        $products = $this->loadData('product_to_add_to_shop');
        foreach ($products as $key => $value) {
            $this->productHelper()->frontOpenProduct($value);
            if ($key != 'grouped') {
                $dataToBuy = $this->loadData('custom_options_to_add_to_shopping_cart');
                $this->productHelper()->frontFillBuyInfo($dataToBuy);
            }
            if ($key != 'simple' && $key != 'virtual') {
                $addData = $this->loadData($key . '_options_to_add_to_shopping_cart');
                $this->productHelper()->frontFillBuyInfo($addData);
            }
            $this->productHelper()->frontAddProductToCart();
        }
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout('multiple_test_data');
    }
}