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
class Tags_FrontendCreateTest extends Mage_Selenium_TestCase
{

    protected function assertPreConditions()
    {
        $this->addParameter('productUrl', '');
    }

    /**
     * <p>Preconditions</p>
     * <p>Create Customer for tests</p>
     *
     * @test
     */
    public function createCustomer()
    {
        //Data
        $userData = $this->loadData('customer_account_for_prices_validation', NULL, 'email');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);

        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Preconditions</p>
     * <p>Creates Category to use during tests</p>
     *
     * @test
     */
    public function createCategory()
    {
        $this->loginAdminUser();
        $this->navigate('manage_categories');
        $this->categoryHelper()->checkCategoriesPage();
        $rootCat = 'Default Category';
        $categoryData = $this->loadData('sub_category_required', null, 'name');
        $this->categoryHelper()->createSubCategory($rootCat, $categoryData);
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->categoryHelper()->checkCategoriesPage();

        return $rootCat . '/' . $categoryData['name'];
    }

    /**
     * <p>Preconditions</p>
     * <p>Create Simple Products for tests</p>
     *
     * @depends createCategory
     * @test
     */
    public function createProduct($category)
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $simpleProductData = $this->loadData('simple_product_for_prices_validation_front_1',
                array('categories' => $category), array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($simpleProductData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        return $simpleProductData['general_name'];
    }

    /**
     * <p>Tag creating with Logged Customer</p>
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
     * @dataProvider dataTagName
     * @depends createCustomer
     * @depends createCategory
     * @depends createProduct
     *
     * @test
     */
    public function frontendTagVerificationLoggedCustomer($dataTagName, $customer, $category, $products)
    {
        //Data
        $verificationData = $this->loadData('new_tag_double',
                array('product_name' => $products, 'new_tag_names' => $dataTagName));
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->productHelper()->frontOpenProduct($products);
        //Steps
        $this->tagsHelper()->frontendAddTag($verificationData);
        //Verification
        $this->assertTrue($this->successMessage('tag_accepted_success'), $this->messages);
        $this->tagsHelper()->frontendTagVerification($verificationData);
        $this->navigate('my_account_my_tags');
        $this->tagsHelper()->frontendDeleteTag($verificationData);
    }

    /**
     * <p>Tags Verification in Category</p>
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
     * @depends createCategory
     * @depends createProduct
     *
     * @test
     */
    public function frontendTagVerificationInCategory($customer, $category, $products){
        //Data
        $verificationData = $this->loadData('new_tag_double',
                array('product_name' => $products, 'category' => $category));
        $subCategory = explode('/', $category);
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->productHelper()->frontOpenProduct($products);
        //Steps
        $this->tagsHelper()->frontendAddTag($verificationData);
        //Verification
        $this->assertTrue($this->successMessage('tag_accepted_success'), $this->messages);
        $this->logoutCustomer();
        $this->loginAdminUser();
        $this->navigate('pending_tags');
        $tags = explode(' ', $verificationData['new_tag_names']);
        $searchData = array();
        foreach($tags as $tagName) {
            $tagToApprove = array('tag_name' => $tagName);
            $searchData[] = $tagToApprove;
        }
        $this->tagsHelper()->changeTagsStatus($searchData, 'Approved');
        $this->frontend();
        $this->tagsHelper()->frontendTagVerificationInCategory($verificationData);
    }

    public function dataTagName()
    {
        return array(
            array("'aaaaaa'"),
            array("aaaaaa"),
            array('aaaaaa'),
            array("aaaaqwe aaaaaadddddd"),
            array("'aaaaqwe aaaaaadddddd'"),
            array('aaaaqwe aaaaaadddddd'),
            array("'ddddd''dddddddd sdfd ''2'")
        );
    }

    /**
     * Tag creating with Not Logged Customer
     *
     * <p>1. Goto Frontend</p>
     * <p>2. Open created product</p>
     * <p>3. Add Tag to product</p>
     * <p>4. Login page opened</p>
     *
     * @depends createCategory
     * @depends createProduct
     *
     * @test
     */
    public function frontendTagVerificationNotLoggedCustomer($category, $products)
    {
        //Data
        $verificationData = $this->loadData('new_tag_single', array('product_name' => $products));
        //Preconditions
        $this->logoutCustomer();
        $this->productHelper()->frontOpenProduct($products, $category); #Need to reindex data for correct usage
        //Steps
        $this->tagsHelper()->frontendAddTag($verificationData, FALSE);
    }
}
