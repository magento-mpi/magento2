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
 * Prices Validation on the frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tags_CreateTest extends Mage_Selenium_TestCase
{

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Create Customer for tests</p>
     *
     * @test
     */
    public function createCustomer()
    {
        //Data
        $userData = $this->loadData('customer_account_for_prices_validation', NULL, 'email');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);

        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Create Simple Products for tests</p>
     *
     * @test
     */
    public function createProduct()
    {
        $this->navigate('manage_products');
        $simpleProductData = $this->loadData('simple_product_for_prices_validation_front_1', NULL,
                array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($simpleProductData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        return $simpleProductData['general_name'];
    }

    /**
     * <p>Create Tag</p>
     *
     * <p>1. Login to Frontend</p>
     * <p>2. Open created product</p>
     * <p>3. Add Tag to product</p>
     * <p>4. Check confirmation message</p>
     * <p>5. Goto "My Account"</p>
     * <p>6. Check tag displaying in "My Recent Tags"</p>
     * <p>7. Goto "My Tags" tab</p>
     * <p>8. Check tag displaying on the page</p>
     * <p>9. Open current tag - page with assigned product opens</p>
     * <p>10. Tag is assigned to correct product</p>
     *
     * @depends createCustomer
     * @depends createProduct
     *
     * @test
     */
    public function validateTagFrontend($customer, $products)
    {
        //Data
        $verificationData = $this->loadData('new_tag', array('product_name' => $products), 'new_tag_names');
        $this->addParameter('productUrl', $products);
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->productHelper()->frontOpenProduct($products);
        //Steps
        $this->tagsHelper()->createTag($verificationData);
        //Verification
        $this->assertTrue($this->successMessage('tag_accepted_success'), $this->messages);
        $this->tagsHelper()->tagVerificationFront($verificationData);
    }

}
