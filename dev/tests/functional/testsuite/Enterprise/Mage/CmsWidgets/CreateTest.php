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
class Enterprise_Mage_CmsWidgets_CreateTest extends Mage_Selenium_TestCase
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
     * @test
     */
    public function preconditionsForTests()
    {
        $bannerData = $this->loadDataSet('CmsBanners', 'new_cms_banner_req');
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
        //Creating Banner
        $this->navigate('manage_cms_banners');
        $this->cmsBannersHelper()->createCmsBanner($bannerData);
        $this->assertMessagePresent('success', 'success_saved_cms_banner');

        return array(
            'banner_name' => $bannerData['banner_properties']['banner_properties_name'],
            'override' => array(
                'anchor' => $category['parent_category'] . '/' . $category['name'],
                'not_anchor' => $productData['category']['path'],
                'product_1' => $productData['simple']['product_sku'],
                'product_2' => $grouped['general_sku'],
                'product_3' => $productData['configurable']['product_sku'],
                'product_4' => $productData['virtual']['product_sku'],
                'product_5' => $bundle['general_sku'],
                'product_6' => $productData['downloadable']['product_sku']
            )
        );
    }

    /**
     * <p>Creates All Types of widgets</p>
     *
     * @param string $dataWidgetType
     * @param array $testData
     *
     * @test
     *
     * @dataProvider widgetTypesDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3229
     */
    public function createAllTypesOfWidgetsAllFields($dataWidgetType, $testData)
    {
        //Data
        $widgetData = $this->loadDataSet('CmsWidget', $dataWidgetType . '_widget', null, $testData['override']);
        if ($dataWidgetType == 'banner_rotator') {
            $widgetData['widget_options']['banner_name'] = $testData['banner_name'];
        }
        //Steps
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->createWidget($widgetData);
        //Verifying
        $this->assertMessagePresent('success', 'successfully_saved_widget');
    }

    public function widgetTypesDataProvider()
    {
        return array(
            array('banner_rotator'),
            array('catalog_events_carousel'),
            array('giftregistry_search'),
            array('wishlist_search'),
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
            $override = array(
                'filter_sku' => $testData['products']['product_3'],
                'category_path' => $testData['category']['category_path']
            );
        } elseif ($dataWidgetType == 'catalog_category_link') {
            $override = array('category_path' => $testData['category']['category_path']);
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
            $override = array(
                'filter_sku' => $testData['products']['product_3'],
                'category_path' => $testData['category']['category_path']
            );
        } elseif ($dataWidgetType == 'catalog_category_link') {
            $override = array('category_path' => $testData['category']['category_path']);
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
            array('banner_rotator', 'widget_instance_title', 'field'),
            array('banner_rotator', 'select_display_on', 'dropdown'),
            array('banner_rotator', 'select_block_reference', 'dropdown'),
            array('catalog_events_carousel', 'widget_instance_title', 'field'),
            array('catalog_events_carousel', 'frame_size', 'field'),
            array('catalog_events_carousel', 'scroll', 'field'),
            array('catalog_events_carousel', 'select_display_on', 'dropdown'),
            array('catalog_events_carousel', 'select_block_reference', 'dropdown'),
            array('giftregistry_search', 'widget_instance_title', 'field'),
            array('giftregistry_search', 'quick_search_form', 'multiselect'),
            array('giftregistry_search', 'select_display_on', 'dropdown'),
            array('giftregistry_search', 'select_block_reference', 'dropdown'),
            array('wishlist_search', 'widget_instance_title', 'field'),
            array('wishlist_search', 'quick_search_form', 'multiselect'),
            array('wishlist_search', 'select_display_on', 'dropdown'),
            array('wishlist_search', 'select_block_reference', 'dropdown'),
        );
    }
}