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
 * @TODO
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Category_Create_RootCategoryTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Login to backend</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Categories</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_categories');
        $this->assertTrue($this->checkCurrentPage('manage_categories'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }

    /**
     * <p>Creating Root Category with required fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add Root Category" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Root Category created, success message appears</p>
     *
     * @test
     */
    public function rootCategoryWithRequiredFieldsOnly()
    {
        //Data
        $categoryData = $this->loadData('root_category', null, 'name');
        //Steps
        $this->categoryHelper()->createRootCategory($categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_categories'),
                'After successful product creation should be redirected to Manage Products page');
        $this->clickButton('reset', false);
    }

    /**
     * <p>Tests are failed due to MAGE-4385.</p>
     * <p>Creating Root Category with required fields empty</p>
     * <p>Steps</p>
     * <p>1. Click "Add Root Category" button </p>
     * <p>2. Fill in necessary fields, leave required fields empty</p>
     * <p>3. Click "Save Category" button</p>
     * <p>Expected Result:</p>
     * <p>Root Category not created, error message appears</p>

     *
     * @dataProvider dataEmptyFields
     * @test
     */
    public function rootCategoryWithRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data
        $overrideData = array($emptyField => '');
        $categoryData = $this->loadData('root_category', $overrideData);
        //Steps
        $this->categoryHelper()->createRootCategory($categoryData);
        //Verifying
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
        print_r($categoryData);
    }

    public function dataEmptyFields()
    {
        return array(
            array('name', 'field'),
            array('available_product_listing', 'multiselect')
        );
    }

}
