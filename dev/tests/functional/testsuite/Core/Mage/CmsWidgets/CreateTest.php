<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsWidgets
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create Widget Test
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CmsWidgets_CreateTest extends Mage_Selenium_TestCase
{
    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->deleteAllWidgets();
        $this->flushCache();
    }

    /**
     * @return array
     *
     * @test
     */
    public function preconditionsForTests()
    {
        $category = $this->loadDataSet('Category', 'sub_category_required', array('is_anchor' => 'Yes'));
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->navigate('manage_products');
        $this->runMassAction('Delete', 'all');
        $productData = $this->productHelper()->createConfigurableProduct(true);
        $categoryPath = $productData['category']['path'];
        $bundle = $this->loadDataSet('SalesOrder', 'fixed_bundle_for_order',
            array('general_categories' => $categoryPath),
            array(
                'add_product_1' => $productData['simple']['product_sku'],
                'add_product_2' => $productData['virtual']['product_sku']
            )
        );
        $grouped = $this->loadDataSet('SalesOrder', 'grouped_product_for_order',
            array('general_categories' => $categoryPath),
            array(
                'associated_1' => $productData['simple']['product_sku'],
                'associated_2' => $productData['virtual']['product_sku'],
                'associated_3' => $productData['downloadable']['product_sku']
            )
        );
        $this->productHelper()->createProduct($grouped, 'grouped');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($bundle, 'bundle');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'anchor' => $category['parent_category'] . '/' . $category['name'],
            'not_anchor' => $productData['category']['path'],
            'product_1' => $productData['simple']['product_sku'],
            'product_2' => $grouped['general_sku'],
            'product_3' => $productData['configurable']['product_sku'],
            'product_4' => $productData['virtual']['product_sku'],
            'product_5' => $bundle['general_sku'],
            'product_6' => $productData['downloadable']['product_sku']
        );
    }

    /**
     * <p>Creates All Types of widgets</p>
     *
     * @param string $dataWidgetType
     * @param array $testData
     *
     * @test
     * @dataProvider widgetTypesDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3229
     */
    public function createAllTypesOfWidgetsAllFields($dataWidgetType, $testData)
    {
        //Data
        $widgetData = $this->loadDataSet('CmsWidget', $dataWidgetType . '_widget', null, $testData);
        //Steps
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->createWidget($widgetData);
        //Verifying
        $this->assertMessagePresent('success', 'successfully_saved_widget');
    }

    public function widgetTypesDataProvider()
    {
        return array(
            array('cms_page_link'),
            array('cms_static_block'),
            array('catalog_category_link'),
            array('catalog_new_products_list'),
            array('catalog_product_link'),
            array('orders_and_returns'),
            array('recently_compared_products'),
            array('recently_viewed_products')
        );
    }

    /**
     * <p>Creates All Types of widgets with required fields only</p>
     *
     * @param string $dataWidgetType
     * @param array $testData
     *
     * @test
     * @dataProvider widgetTypesDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3230
     */
    public function createAllTypesOfWidgetsReqFields($dataWidgetType, $testData)
    {
        //Data
        $override = array();
        if ($dataWidgetType == 'catalog_product_link') {
            $override = array('filter_sku' => $testData['product_3'], 'category_path' => $testData['not_anchor']);
        } elseif ($dataWidgetType == 'catalog_category_link') {
            $override = array('category_path' => $testData['not_anchor']);
        }
        $widgetData = $this->loadDataSet('CmsWidget', $dataWidgetType . '_widget_req', $override);
        //Steps
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->createWidget($widgetData);
        //Verifying
        $this->assertMessagePresent('success', 'successfully_saved_widget');
    }

    /**
     * <p>Creates All Types of widgets with required fields empty</p>
     *
     * @param string $dataWidgetType
     * @param string $emptyField
     * @param string $fieldType
     * @param array $testData
     *
     * @test
     * @dataProvider withEmptyFieldsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3231
     */
    public function withEmptyFields($dataWidgetType, $emptyField, $fieldType, $testData)
    {
        //Data
        $override = array();
        if ($dataWidgetType == 'catalog_product_link') {
            $override = array('filter_sku' => $testData['product_3'], 'category_path' => $testData['not_anchor']);
        } elseif ($dataWidgetType == 'catalog_category_link') {
            $override = array('category_path' => $testData['not_anchor']);
        }
        if ($fieldType == 'field') {
            $override[$emptyField] = ' ';
        } elseif ($fieldType == 'dropdown') {
            if ($emptyField == 'select_display_on') {
                if ($dataWidgetType == 'cms_page_link' || $dataWidgetType == 'catalog_category_link') {
                    $override['select_template'] = '%noValue%';
                }
                $override['select_block_reference'] = '%noValue%';
            }
            $override[$emptyField] = '-- Please Select --';
        } else {
            $override['widget_options'] = '%noValue%';
            $this->addParameter('elementName', 'Not Selected');
        }
        $widgetData = $this->loadDataSet('CmsWidget', $dataWidgetType . '_widget_req', $override);
        //Steps
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->createWidget($widgetData);
        //Verifying
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withEmptyFieldsDataProvider()
    {
        return array(
            array('cms_page_link', 'widget_instance_title', 'field'),
            array('cms_page_link', 'page_id', 'pageelement'),
            array('cms_page_link', 'select_display_on', 'dropdown'),
            array('cms_page_link', 'select_block_reference', 'dropdown'),
            array('cms_static_block', 'widget_instance_title', 'field'),
            array('cms_static_block', 'block_id', 'pageelement'),
            array('cms_static_block', 'select_display_on', 'dropdown'),
            array('cms_static_block', 'select_block_reference', 'dropdown'),
            array('catalog_category_link', 'widget_instance_title', 'field'),
            array('catalog_category_link', 'category_id', 'pageelement'),
            array('catalog_category_link', 'select_display_on', 'dropdown'),
            array('catalog_category_link', 'select_block_reference', 'dropdown'),
            array('catalog_new_products_list', 'widget_instance_title', 'field'),
            array('catalog_new_products_list', 'number_of_products_to_display', 'field'),
            array('catalog_new_products_list', 'select_display_on', 'dropdown'),
            array('catalog_new_products_list', 'select_block_reference', 'dropdown'),
            array('catalog_product_link', 'widget_instance_title', 'field'),
            array('catalog_product_link', 'category_id', 'pageelement'),
            array('catalog_product_link', 'select_display_on', 'dropdown'),
            array('catalog_product_link', 'select_block_reference', 'dropdown'),
            array('orders_and_returns', 'widget_instance_title', 'field'),
            array('orders_and_returns', 'select_display_on', 'dropdown'),
            array('orders_and_returns', 'select_block_reference', 'dropdown'),
            array('recently_compared_products', 'widget_instance_title', 'field'),
            array('recently_compared_products', 'number_of_products_to_display_compared_and_viewed', 'field'),
            array('recently_compared_products', 'select_display_on', 'dropdown'),
            array('recently_compared_products', 'select_block_reference', 'dropdown'),
            array('recently_viewed_products', 'widget_instance_title', 'field'),
            array('recently_viewed_products', 'number_of_products_to_display_compared_and_viewed', 'field'),
            array('recently_viewed_products', 'select_display_on', 'dropdown'),
            array('recently_viewed_products', 'select_block_reference', 'dropdown')
        );
    }
}