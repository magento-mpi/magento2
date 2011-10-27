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
 * Create Page Test
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CmsPages_CreateTest extends Mage_Selenium_TestCase
{
    protected static $products = array();

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    protected function assertPreconditions()
    {
        $this->addParameter('id', '0');
    }

    /**
     * <p>Preconditions</p>
     * <p>Creates Category to use during tests</p>
     *
     * @test
     */
    public function createCategory()
    {
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
     * <p>Creates Attribute (dropdown) to use during tests</p>
     *
     * @test
     */
    public function createAttribute()
    {
        $attrData = $this->loadData('product_attribute_dropdown_with_options', NULL,
                array('admin_title', 'attribute_code'));
        $associatedAttributes = $this->loadData('associated_attributes',
                array('General' => $attrData['attribute_code']));
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        $this->assertTrue($this->successMessage('success_attribute_set_saved'), $this->messages);

        return $attrData;
    }

    /**
     * Create required products for testing
     *
     * @dataProvider dataProductTypes
     * @depends createCategory
     * @depends createAttribute
     *
     * @test
     */
    public function createProducts($dataProductType, $category, $attrData)
    {
        $this->navigate('manage_products');
        //Data
        if ($dataProductType == 'configurable') {
            $productData = $this->loadData($dataProductType . '_product_required',
                array('configurable_attribute_title' => $attrData['admin_title'],
                    'categories' => $category), array('general_sku', 'general_name'));
        } else {
            $productData = $this->loadData($dataProductType . '_product_required',
                array('categories' => $category), array('general_name', 'general_sku'));
        }
        //Steps
        $this->productHelper()->createProduct($productData, $dataProductType);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        self::$products['sku'][$dataProductType] = $productData['general_sku'];
        self::$products['name'][$dataProductType] = $productData['general_name'];
    }

    public function dataProductTypes()
    {
        return array(
            array('simple')
        );
    }

    /**
     * <p>Creates Page with required fields</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Pages page</p>
     * <p>2. Create page with required fields</p>
     * <p>Expected result</p>
     * <p>Page is created successfully</p>
     *
     * @depends createCategory
     * @test
     */
    public function createPageWithRequiredFields($category)
    {
        $this->navigate('manage_cms_pages');
        $temp = array();
        $temp['filter_sku'] = self::$products['sku']['simple'];
        $temp['category_path'] = $category;
        $pageData = $this->loadData('new_page_req', $temp, array('page_title', 'url_key'));
        $this->cmsPagesHelper()->createPage($pageData);
        $this->cmsPagesHelper()->frontValidatePage($pageData);
    }

    /**
     * <p>Creates Page with all fields and all types of widgets</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Pages page</p>
     * <p>2. Create page with all fields filled and all types of widgets</p>
     * <p>Expected result</p>
     * <p>Page is created successfully</p>
     *
     * @depends createCategory
     * @test
     */
    public function createPageWithAllFields($category)
    {
        $this->navigate('manage_cms_pages');
        $temp = array();
        $temp['filter_sku'] = self::$products['sku']['simple'];
        $temp['category_path'] = $category;
        $pageData = $this->loadData('new_page_all_fields', $temp, array('page_title', 'url_key'));
        $this->cmsPagesHelper()->createPage($pageData);
        $this->cmsPagesHelper()->frontValidatePage($pageData);
    }

    /**
     * <p>Creates Page with all fields filled except one empty</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Manage Pages page</p>
     * <p>2. Create page with all fields filled, but leave one empty</p>
     * <p>Expected result</p>
     * <p>Page is not created successfully</p>
     *
     * @dataProvider dataEmptyAllFields
     * @depends createCategory
     * @test
     */
    public function createPageWithAllFieldsEmpty($emptyFieldName, $emptyFielsType, $category)
    {
        $this->navigate('manage_cms_pages');
        $temp = array();
        $temp['filter_sku'] = self::$products['sku']['simple'];
        $temp['category_path'] = $category;
        if ($emptyFieldName == 'editor_disabled') {
            $temp['content'] = '%noValue%';
        } else {
            if ($emptyFielsType == 'field') {
                $temp[$emptyFieldName] = ' ';
            } elseif ($emptyFielsType == 'dropdown') {
                $temp[$emptyFieldName] = '-- Please Select --';
                $temp['filter_url_key'] = '%noValue%';
            } else {
                $temp['filter_url_key'] = '%noValue%';
                $this->addParameter('elementName', 'Not Selected');
            }
        }
        $pageData = $this->loadData('new_page_req', $temp, array('page_title', 'url_key'));
        print_r($pageData);
        $this->cmsPagesHelper()->createPage($pageData);
    }

    public function dataEmptyAllFields()
    {
        return array(
            array('page_title', 'field'),
            array('url_key', 'field'),
            array('editor_disabled', 'field'),
            array('widget_type', 'dropdown'),
            array('chosen_option', 'pageelement')
        );
    }
}
