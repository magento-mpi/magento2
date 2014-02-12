<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AddBySku
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for Order by SKU functionality on the Frontend via widget
 */
class Enterprise_Mage_AddBySku_FrontendSkuWidgetTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('OrderBySkuSettings/add_by_sku_all');
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->deleteWidget(array('filter_type' => 'Order by SKU'));
        $this->assertMessagePresent('success', 'successfully_deleted_widget');
    }

    /**
     * <p>Creating Simple products and customer</p>
     *
     * @return array
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $widget = $this->loadDataSet('OrderBySkuWidget', 'sku_widget_without_link');
        $user = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps and Verification
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');

        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');

        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->createWidget($widget);
        $this->assertMessagePresent('success', 'successfully_saved_widget');
        $this->flushCache();
        $this->reindexInvalidedData();

        $this->frontend();
        $this->customerHelper()->registerCustomer($user);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();

        return array(
            'category' => $category['name'],
            'simple' => array('sku' => $simple['general_sku'], 'qty' => 1),
            'customer' => array('email' => $user['email'], 'password' => $user['password']),
            'widget' => array(
                'filter_type' => $widget['settings']['type'],
                'filter_title' => $widget['frontend_properties']['widget_instance_title']
            )
        );
    }

    /**
     * <p>Create Order by SKU type widget with disable widget options</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3974
     */
    public function checkSkuWidgetWithoutLink($testData)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['category']);
        //Verifying
        $this->assertFalse($this->controlIsPresent('link', 'sku_link'), 'There is a link on the Order by SKU widget.');
    }

    /**
     * <p>Deleting Widget of the Order by SKU type</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3980
     */
    public function deleteSkuWidget($testData)
    {
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->deleteWidget($testData['widget']);
        $this->assertMessagePresent('success', 'successfully_deleted_widget');
    }

    /**
     * <p>Create Order by SKU type widget with enable widget options</p>
     *
     * @return array
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3976
     */
    public function preconditionsForTestsWidgetWithLink()
    {
        $widget = $this->loadDataSet('OrderBySkuWidget', 'sku_widget_with_link');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->createWidget($widget);
        $this->assertMessagePresent('success', 'successfully_saved_widget');
        $this->flushCache();
        $this->reindexInvalidedData();

        return array(
            'filter_type' => $widget['settings']['type'],
            'filter_title' => $widget['frontend_properties']['widget_instance_title']
        );
    }

    /**
     * <p>Create Order by SKU type widget with enable widget options</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @depends preconditionsForTestsWidgetWithLink
     * @TestlinkId TL-MAGE-3976
     */
    public function checkSkuWidgetWithLink($testData)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['category']);
        //Verifying
        $this->assertTrue($this->controlIsPresent('link', 'sku_link'),
            'There is not a link on the Order by SKU widget.');
    }

    /**
     * <p>Displaying Link Text in the widget for everyone customer groups</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @depends checkSkuWidgetWithLink
     * @TestlinkId TL-MAGE-3977
     */
    public function clickLinkOnWidget($testData)
    {
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['customer']);
        $this->categoryHelper()->frontOpenCategory($testData['category']);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array($testData['simple']));
        $this->clickControl('link', 'sku_link');
        //Verifying
        $this->assertTrue($this->checkCurrentPage('order_by_sku'), 'This is not Order by SKU page');
        $this->assertSame('', $this->getControlAttribute('field', 'sku', 'value'),
            'SKU filed is not empty');
        $this->assertSame('', $this->getControlAttribute('field', 'qty', 'value'),
            'Qty filed is not empty');
    }

    /**
     * <p>Displaying Link Text in the widget for non logged in user</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4003
     */
    public function widgetLinkForGuest($testData)
    {
        //Steps
        $this->logoutCustomer();
        $this->categoryHelper()->frontOpenCategory($testData['category']);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array($testData['simple']));
        $this->clickControl('link', 'sku_link');
        //Verifying
        $this->assertTrue($this->checkCurrentPage('customer_login'),
            'Guest should be redirected to Login or Create an Account page');
    }

    /**
     * <p>Editing Link Test in the widget</p>
     *
     * @param string $testData
     * @param array $selectWidget
     *
     * @test
     * @depends preconditionsForTests
     * @depends preconditionsForTestsWidgetWithLink
     * @TestlinkId TL-MAGE-3979
     */
    public function editWidgetLink($testData, $selectWidget)
    {
        //Data
        $widgetOptions = $this->loadDataSet('OrderBySkuWidget', 'widget_options_for_edit');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->openWidget($selectWidget);
        $this->cmsWidgetsHelper()->fillWidgetOptions($widgetOptions);
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'successfully_saved_widget');
        $this->flushCache();
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['category']);
        //Verifying
        $this->assertSame($widgetOptions['sku_link_text'], $this->getControlAttribute('link', 'sku_link', 'text'),
            'The link on the Order by SKU widget is not changed.');
    }

    /**
     * <p>Valid values for QTY field according SRS</p>
     *
     * @param string $qty
     * @param string $message
     * @param string $messageType
     * @param array $testData
     *
     * @test
     * @dataProvider qtyListDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3933, TL-MAGE-3889
     */
    public function qtyValidation($qty, $message, $messageType, $testData)
    {
        //Steps:
        $this->customerHelper()->frontLoginCustomer($testData['customer']);
        $this->categoryHelper()->frontOpenCategory($testData['category']);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(
            array(array('sku' => $testData['simple']['sku'], 'qty' => $qty))
        );
        $this->saveForm('add_to_cart_by_sku');
        //Verifying
        if ($messageType == 'validation') {
            $this->addFieldIdToMessage('field', 'qty');
            $this->assertMessagePresent('validation', $message);
        } else {
            $this->assertTrue($this->controlIsVisible('fieldset', 'products_requiring_attention'));
            $this->assertMessagePresent('error', 'required_attention_product');
            $this->assertMessagePresent('error', $message);
        }
    }

    public function qtyListDataProvider()
    {
        return array(
            array('non-num', 'enter_greater_than_zero', 'validation'),
            array('-5', 'enter_greater_than_zero', 'validation'),
            array('0', 'enter_greater_than_zero', 'validation'),
            array('', 'empty_required_field', 'validation'),
            array('0.00001', 'enter_valid_qty', 'error'),
            array('99999999.9999', 'max_allowed_qty', 'error')
        );
    }

    /**
     * <p>Validation rows, for which SKU and Qty values are empty</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4019
     */
    public function addEmptyRowQtyFields($testData)
    {
        //Steps:
        $this->customerHelper()->frontLoginCustomer($testData['customer']);
        $this->categoryHelper()->frontOpenCategory($testData['category']);
        $this->saveForm('add_to_cart_by_sku');
        //Verifying
        $this->addFieldIdToMessage('field', 'sku');
        $this->assertMessagePresent('validation', 'empty_required_field');
    }

    /**
     * <p>Validation rows, for which SKU and Qty values are empty</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4019
     */
    public function addSimpleProductWithEmptyRow($testData)
    {
        //Steps:
        $this->customerHelper()->frontLoginCustomer($testData['customer']);
        $this->categoryHelper()->frontOpenCategory($testData['category']);
        $this->addFieldIdToMessage('field', 'sku');
        $this->clickButton('add_row', false);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array($testData['simple']));
        $this->saveForm('add_to_cart_by_sku');
        //Verifying
        $this->assertMessagePresent('validation', 'empty_required_field');
    }

    /**
     * <p>Adding to Cart by SKU after entering SKUs and Qty's manually</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4088
     */
    public function addSimpleProduct($testData)
    {
        //Steps:
        $this->customerHelper()->frontLoginCustomer($testData['customer']);
        $this->categoryHelper()->frontOpenCategory($testData['category']);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array($testData['simple']));
        $this->saveForm('add_to_cart_by_sku');
        //Verifying
        $this->addParameter('productName', '1 product');
        $this->assertMessagePresent('success', 'product_added_to_cart');
    }

    /**
     * <p>Adding to Cart by SKU after entering values in multiple fields</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4102
     */
    public function addMultipleSimpleProducts($testData)
    {
        //Steps:
        $this->customerHelper()->frontLoginCustomer($testData['customer']);
        $this->categoryHelper()->frontOpenCategory($testData['category']);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array($testData['simple'], $testData['simple']));
        $this->saveForm('add_to_cart_by_sku');
        //Verifying
        $this->addParameter('productName', '2 products');
        $this->assertMessagePresent('success', 'product_added_to_cart');
    }

    /**
     * <p>Adding to Cart by SKU after entering valid and invalid values in multiple fields</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4103
     */
    public function addMultipleSimpleProductsFailure($testData)
    {
        //Steps:
        $this->customerHelper()->frontLoginCustomer($testData['customer']);
        $this->categoryHelper()->frontOpenCategory($testData['category']);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array(
            $testData['simple'],
            array('sku' => $testData['simple']['sku'], 'qty' => '-15'),
        ));
        $this->saveForm('add_to_cart_by_sku');
        //Verifying
        $this->addFieldIdToMessage('field', 'qty');
        $this->assertMessagePresent('validation', 'enter_greater_than_zero');
    }

    /**
     * <p>Displaying Link Text in the widget for specified customer group</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4002
     */
    public function widgetLinkForSpecifiedCustomer($testData)
    {
        //Preconditions:
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('OrderBySkuSettings/add_by_sku_general_group');
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['customer']);
        $this->categoryHelper()->frontOpenCategory($testData['category']);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array($testData['simple']));
        $this->clickControl('link', 'sku_link');
        //Verifying
        $this->assertTrue($this->checkCurrentPage('order_by_sku'), 'This is not Order by SKU page');
        $this->assertSame('', $this->getControlAttribute('field', 'sku', 'value'),
            'SKU filed is not empty');
        $this->assertSame('', $this->getControlAttribute('field', 'qty', 'value'),
            'Qty filed is not empty');
    }

    /**
     * <p>Displaying Link Text in the widget for specified customer group</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @depends widgetLinkForSpecifiedCustomer
     * @TestlinkId TL-MAGE-4002
     */
    public function widgetLinkForUnspecifiedCustomer($testData)
    {
        //Preconditions:
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('OrderBySkuSettings/add_by_sku_retailer_group');
        $this->clearInvalidedCache();
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['customer']);
        $this->categoryHelper()->frontOpenCategory($testData['category']);
        //Verifying
        $this->assertFalse($this->controlIsPresent('link', 'sku_link'),
            'The link on the Order by SKU widget should not be present.');
    }

    /**
     * <p>Displaying Link Text in the widget with disabled SKU functionality on Frontend</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function widgetLinkForDisabledFrontendSku($testData)
    {
        //Preconditions:
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('OrderBySkuSettings/add_by_sku_disabled');
        $this->clearInvalidedCache();
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['category']);
        //Verifying
        $this->assertFalse($this->controlIsPresent('link', 'sku_link'),
            'The link on the Order by SKU widget should not be present.');
    }
}
