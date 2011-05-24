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
 * Delete product attributes
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductAttribute_DeleteTest extends Mage_Selenium_TestCase
{

    /**
     * Preconditions:
     * Admin user should be logged in.
     * Should stays on the Admin Dashboard page after login.
     * Navigate to System -> Manage Customers.
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->assertTrue($this->checkCurrentPage('dashboard'),
                'Wrong page is opened');
        $this->navigate('manage_attributes');
        $this->assertTrue($this->checkCurrentPage('manage_attributes'),
                'Wrong page is opened');
    }

    /**
     * Delete Product Attributes
     *
     * Steps:
     * 1.Click on "Add New Attribute" button
     * 2.Fill all required fields
     * 3.Click on "Save Attribute" button
     * 4.Search and open attribute
     * 5.Click on "Delete Attribute" button
     *
     * Expected result:
     * Attribute successfully deleted.
     * Success message: 'The product attribute has been deleted.' is displayed.
     *
     * @dataProvider data_DataName
     */
    public function test_DeleteProductAttribute_Deletable($dataName)
    {
        //Data
        $attrData = $this->loadData($dataName, null, array('attribute_code', 'admin_title'));
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_code' => $attrData['attribute_code'],
                            'attribute_lable' => $attrData['admin_title'],
                            'scope' => '',
                            'use_in_layered_navigation' => ''
                ));
        //Step 1. Create attribute
        $this->clickButton('add_new_attribute');
        $this->fillForm($attrData, 'properties');
        $this->clickControl('tab', 'manage_lables_options', false);
        $this->fillForm($attrData, 'manage_lables_options');
        $this->manageLabelsAndOptionsForStoreView($attrData);
        $this->manageAttributeOptions($attrData);
        $this->saveForm('save_attribute');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_attributes'),
                'After successful customer creation should be redirected to Manage Attributes page');
        //Step 2. Open attribute and delete.
        $this->clickButton('reset_filter');
        $this->navigate('manage_attributes');
        $this->assertTrue($this->searchAndOpen($searchData), 'Attribute is not found');
        $this->deleteElement('delete_attribute', 'delete_confirm_message');
        //Verifying
        $this->assertTrue($this->successMessage('success_deleted_attribute'), $this->messages);
    }

    public function data_DataName()
    {
        return array(
            array('product_attribute_textfield'),
            array('product_attribute_textarea'),
            array('product_attribute_date'),
            array('product_attribute_yesno'),
            array('product_attribute_multiselect'),
            array('product_attribute_dropdown'),
            array('product_attribute_price'),
            array('product_attribute_mediaimage'),
            array('product_attribute_fpt')
        );
    }

    /**
     * Delete system Product Attributes
     *
     * Steps:
     * 1.Search and open system attribute.
     *
     * Expected result:
     * "Delete Attribute" button isn't present.
     */
    public function test_ThatCannotBeDeleted_SystemAttribute()
    {
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_code' => 'price',
                            'attribute_lable' => 'Price',
                            'required' => 'Yes',
                            'system' => 'Yes',
                            'visible' => 'No',
                            'scope' => 'Website',
                            'searchable' => 'Yes',
                            'use_in_layered_navigation' => 'Filterable (with results)',
                            'comparable' => 'No',
                ));
        //Step.
        $this->clickButton('reset_filter');
        $this->navigate('manage_attributes');
        $this->assertTrue($this->searchAndOpen($searchData), 'Attribute is not found');
        //Verifying
        $this->assertFalse($this->controlIsPresent('button', 'delete_attribute'),
                '"Delete Attribute" button is present on the page');
    }

    /**
     * @TODO Waiting a tests for Configurable products
     */
    public function test_ThatCannotBeDeleted_DropdownAttributeUsedInConfigurableProduct()
    {
        //  @TODO Waiting a tests for Configurable products
        $this->markTestIncomplete();
    }

    /**
     * *********************************************
     * *         HELPER FUNCTIONS                  *
     * *********************************************
     */

    /**
     * Fill in(or verify) 'Title' field for different Store Views.
     *
     * PreConditions: attribute page is opened on 'Manage Label / Options' tab.
     *
     * @param array $attrData
     * @param string $action
     */
    public function manageLabelsAndOptionsForStoreView($attrData, $action = 'fill', $type ='titles')
    {
        $page = $this->getCurrentLocationUimapPage();
        $dataArr = array();
        switch ($type) {
            case 'titles':
                $fieldSet = $page->findFieldset('manage_titles');
                foreach ($attrData as $f_key => $d_value) {
                    if (preg_match('/title/', $f_key) and is_array($attrData[$f_key])) {
                        reset($attrData[$f_key]);
                        $key = current($attrData[$f_key]);
                        $value = next($attrData[$f_key]);
                        $dataArr[$key] = $value;
                    }
                }
                break;
            case 'options':
                $fieldSet = $page->findFieldset('manage_options');
                foreach ($attrData as $f_key => $d_value) {
                    if (preg_match('/option/', $f_key) and is_array($attrData[$f_key])) {
                        foreach ($attrData[$f_key] as $k1 => $v2) {
                            if (is_array($attrData[$f_key][$k1]) and preg_match('/store_view_option_name/', $k1)) {
                                reset($attrData[$f_key][$k1]);
                                $key = current($attrData[$f_key][$k1]);
                                $value = next($attrData[$f_key][$k1]);
                                $dataArr[$key] = $value;
                            }
                        }
                    }
                }
                break;
        }
        $xpath = $fieldSet->getXPath();
        $qtyStore = $this->getXpathCount($xpath . '//th');
        foreach ($dataArr as $k => $v) {
            $number = -1;
            for ($i = 1; $i <= $qtyStore; $i++) {
                if ($this->getText($xpath . "//th[$i]") == $k) {
                    $number = $i;
                    break;
                }
            }
            if ($number != -1) {
                switch ($type) {
                    case 'titles':
                        $this->addParameter('fieldTitleNumber', $number);
                        $fieldName = 'title_by_store_name';
                        break;
                    case 'options':
                        $this->addParameter('storeViewID', $number);
                        $fieldName = 'option_name_by_store_name';
                        break;
                }

                $page->assignParams($this->_paramsHelper);
                switch ($action) {
                    case 'fill':
                        $this->type($xpath . $page->findField($fieldName), $v);
                        break;
                    case 'verify':
                        $this->assertEquals($this->getValue($xpath . $page->findField($fieldName)),
                                $v, 'Stored data not equals to specified');
                        break;
                }
            } else {
                throw new OutOfRangeException("Can't find specified store view.");
            }
        }
    }

    public function manageAttributeOptions($attrData, $action = 'fill')
    {
        $page = $this->getCurrentLocationUimapPage();
        $fieldSet = $page->findFieldset('manage_options');
        $fieldSetXpath = $fieldSet->getXPath();
        foreach ($attrData as $key => $value) {
            if (preg_match('/option/', $key) and is_array($attrData[$key])) {
                if ($this->isElementPresent($fieldSetXpath)) {
                    $optionCount = $this->getXpathCount($fieldSetXpath . "//tr[contains(@class,'option-row')]");
                    $this->addParameter('fieldOptionNumber', $optionCount);
                    $page->assignParams($this->_paramsHelper);
                    $this->clickButton('add_option', FALSE);
                    $this->fillForm($attrData[$key], 'manage_lables_options');
                    $this->manageLabelsAndOptionsForStoreView($attrData, $action, 'options');
                }
            }
        }
    }

}
