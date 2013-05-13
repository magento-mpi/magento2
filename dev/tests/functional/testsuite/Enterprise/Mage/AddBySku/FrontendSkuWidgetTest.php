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
    /**
     * <p>Enable Order By SKU functionality on Frontend</p>
     */
    public function setUpBeforeTests()
    {
        //Data
        $config = $this->loadDataSet('OrderBySkuSettings', 'add_by_sku_all');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->deleteWidget(array('filter_type' => 'Order by SKU'));
        //Verifying        
        $this->assertMessagePresent('success', 'successfully_deleted_widget');
    }

    /**
     * <p>Creating Category to use during tests</p>
     *
     * @test
     * @return string
     */
    public function createCategory()
    {
        //Data
        $categoryData = $this->loadDataSet('Category', 'sub_category_required');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($categoryData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();

        return $categoryData['name'];
    }

    /**
     * <p>Creating Simple products and customer</p>
     *
     * @test
     * @depends createCategory
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps and Verification
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array(
            'simple_product' => array('sku' => $simple['general_sku'], 'qty' => 1),
            'customer' => array('email' => $userData['email'], 'password' => $userData['password'])
        );
    }

    /**
     * <p>Create Order by SKU type widget with disable widget options</p>
     *
     * @test
     * @depends preconditionsForTests
     * @return array
     * @TestlinkId TL-MAGE-3974
     */
    public function createSkuWidgetWithoutLink()
    {
        //Precondition
        $this->loginAdminUser();
        $widgetData = $this->loadDataSet('OrderBySkuWidget', 'sku_widget_without_link');
        //Steps
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->createWidget($widgetData);
        //Verifying
        $this->assertMessagePresent('success', 'successfully_saved_widget');
        $this->clearInvalidedCache();

        return array(
            'filter_type' => $widgetData['settings']['type'],
            'filter_title' => $widgetData['frontend_properties']['widget_instance_title']
        );
    }

    /**
     * <p>Create Order by SKU type widget with disable widget options</p>
     *
     * @param string $category
     *
     * @test
     * @depends createCategory
     * @depends createSkuWidgetWithoutLink
     * @TestlinkId TL-MAGE-3974
     */
    public function checkSkuWidgetWithoutLink($category)
    {
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        //Verifying
        $this->assertFalse($this->controlIsPresent('link', 'sku_link'), 'There is a link on the Order by SKU widget.');
    }

    /**
     * <p>Deleting Widget of the Order by SKU type</p>
     *
     * @param array $widgetToDelete
     *
     * @test
     * @depends createSkuWidgetWithoutLink
     * @TestlinkId TL-MAGE-3980
     */
    public function deleteSkuWidget($widgetToDelete)
    {
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->deleteWidget($widgetToDelete);
        //Verifying        
        $this->assertMessagePresent('success', 'successfully_deleted_widget');
    }

    /**
     * <p>Create Order by SKU type widget with enable widget options</p>
     *
     * @test
     * @depends preconditionsForTests
     * @return array
     * @TestlinkId TL-MAGE-3976
     */
    public function createSkuWidgetWithLink()
    {
        //Preconditions
        $this->loginAdminUser();
        $widgetData = $this->loadDataSet('OrderBySkuWidget', 'sku_widget_with_link');
        //Steps
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->createWidget($widgetData);
        //Verifying
        $this->assertMessagePresent('success', 'successfully_saved_widget');
        $this->clearInvalidedCache();

        return array(
            'filter_type' => $widgetData['settings']['type'],
            'filter_title' => $widgetData['frontend_properties']['widget_instance_title']
        );
    }

    /**
     * <p>Create Order by SKU type widget with enable widget options</p>
     *
     * @param string $category
     *
     * @test
     * @depends createCategory
     * @depends createSkuWidgetWithLink
     * @TestlinkId TL-MAGE-3976
     */
    public function checkSkuWidgetWithLink($category)
    {
        //Steps        
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        //Verifying
        $this->assertTrue($this->controlIsPresent('link', 'sku_link'),
            'There is not a link on the Order by SKU widget.');
    }

    /**
     * <p>Displaying Link Text in the widget for everyone customer groups</p>
     *
     * @param string $category
     * @param array $data
     *
     * @test
     * @depends createCategory
     * @depends preconditionsForTests
     * @depends checkSkuWidgetWithLink
     * @TestlinkId TL-MAGE-3977
     */
    public function clickLinkOnWidget($category, $data)
    {
        //Steps
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->categoryHelper()->frontOpenCategory($category);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple_product']);
        $this->clickControl('link', 'sku_link');
        //Verifying
        $this->assertTrue($this->checkCurrentPage('order_by_sku'), 'This is not Order by SKU page');
        $this->assertTrue($this->getControlAttribute('field', 'sku', 'value') == '',
            'SKU filed is not empty');
        $this->assertTrue($this->getControlAttribute('field', 'qty', 'value') == '',
            'Qty filed is not empty');
    }

    /**
     * <p>Displaying Link Text in the widget for non logged in user</p>
     *
     * @param string $category
     *
     * @test
     * @depends createCategory
     * @depends clickLinkOnWidget
     * @TestlinkId TL-MAGE-4003
     */
    public function widgetLinkForGuest($category)
    {
        //Steps
        $this->logoutCustomer();
        $this->categoryHelper()->frontOpenCategory($category);
        $this->clickControl('link', 'sku_link');
        //Verifying
        $this->assertTrue($this->checkCurrentPage('customer_login'),
            'Guest should be redirected to Login or Create an Account page');
    }

    /**
     * <p>Editing Link Test in the widget</p>
     *
     * @param string $category
     * @param array $selectWidget
     *
     * @test
     * @depends createCategory
     * @depends createSkuWidgetWithLink
     * @depends clickLinkOnWidget
     * @TestlinkId TL-MAGE-3979
     */
    public function editWidgetLink($category, $selectWidget)
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
        $this->clearInvalidedCache();
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        //Verifying    
        $this->assertTrue($this->getControlAttribute('link', 'sku_link', 'value') == $widgetOptions['sku_link_text'],
            'The link on the Order by SKU widget is not changed. ');
    }

    /**
     * <p>Displaying Link Text in the widget for specified customer group</p>
     *
     * @param string $category
     * @param array $data
     *
     * @test
     * @depends createCategory
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4002
     */
    public function widgetLinkForSpecifiedCustomer($category, $data)
    {
        //Preconditions:
        $config = $this->loadDataSet('OrderBySkuSettings', 'add_by_sku_general_group');
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        //Steps
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->categoryHelper()->frontOpenCategory($category);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple_product']);
        $this->clickControl('link', 'sku_link');
        //Verifying
        $this->assertTrue($this->checkCurrentPage('order_by_sku'), 'This is not Order by SKU page');
        $this->assertTrue($this->getControlAttribute('field', 'sku', 'text') == '',
            'SKU filed is not empty');
        $this->assertTrue($this->getControlAttribute('field', 'qty', 'text') == '',
            'Qty filed is not empty');
    }

    /**
     * <p>Displaying Link Text in the widget for specified customer group</p>
     *
     * @param string $category
     * @param array $data
     *
     * @test
     * @depends createCategory
     * @depends preconditionsForTests
     * @depends widgetLinkForSpecifiedCustomer
     * @TestlinkId TL-MAGE-4002
     */
    public function widgetLinkForUnspecifiedCustomer($category, $data)
    {
        //Preconditions:
        $config = $this->loadDataSet('OrderBySkuSettings', 'add_by_sku_retailer_group');
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        //Steps
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->categoryHelper()->frontOpenCategory($category);
        //Verifying
        $this->assertFalse($this->controlIsPresent('link', 'sku_link'),
            'The link on the Order by SKU widget should not be present.');
    }

    /**
     * <p>Displaing Link Text in the widget with disabled SKU functionality on Frontend</p>
     *
     * @param string $category
     *
     * @test
     * @depends createCategory
     */
    public function widgetLinkForDisabledFrontendSku($category)
    {
        //Preconditions:
        $config = $this->loadDataSet('OrderBySkuSettings', 'add_by_sku_disabled');
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        //Verifying
        $this->assertFalse($this->controlIsPresent('link', 'sku_link'),
            'The link on the Order by SKU widget should not be present.');
    }

    /**
     * <p>Valid values for QTY field according SRS</p>
     *
     * @param string $qty
     * @param string $message
     * @param string $category
     * @param array $data
     *
     * @test
     * @dataProvider qtyListDataProvider
     * @depends createCategory
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3933, TL-MAGE-3889
     */
    public function qtyValidation($qty, $message, $category, $data)
    {
        //Steps:
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array('sku' => $data['simple_product']['sku'], 'qty' => $qty));
        $this->clickButton('add_to_cart_by_sku', false);
        $this->waitForAjax();
        //Verifying
        $this->addFieldIdToMessage('field', 'qty');
        $this->assertMessagePresent('validation', $message);
    }

    public function qtyListDataProvider()
    {
        return array(
            array('non-num', 'empty_required_field'),
            array('-5', 'empty_required_field'),
            array('0', 'empty_required_field'),
            array('0.00001', 'empty_required_field'),
            array('999999999.9999', 'empty_required_field'),
            array('-5', 'empty_required_field'),
            array('', 'empty_required_field')
        );
    }

    /**
     * <p>Validation rows, for which SKU and Qty values are empty</p>
     *
     * @param array $category
     *
     * @test
     * @depends createCategory
     * @TestlinkId TL-MAGE-4019
     */
    public function addEmptyRowQtyFields($category)
    {
        //Steps:
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        $this->clickButton('add_to_cart_by_sku');
        //Verifying
        $this->assertMessagePresent('error', 'no_product_added_from_widget');
    }

    /**
     * <p>Validation rows, for which SKU and Qty values are empty</p>
     *
     * @param string $category
     * @param array $data
     *
     * @test
     * @depends createCategory
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4019
     */
    public function addSimpleProductWithEmptyRow($category, $data)
    {
        //Steps:
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        $this->clickButton('add_row_by_sku', false);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple_product']);
        $this->clickButton('add_to_cart_by_sku');
        //Verifying
        $this->assertMessagePresent('success', 'product_added_to_cart_by_sku');
    }

    /**
     * <p>Adding to Cart by SKU after entering SKUs and Qtys manually</p>
     *
     * @param string $category
     * @param array $data
     *
     * @test
     * @depends createCategory
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4088
     */
    public function addSimpleProduct($category, $data)
    {
        //Steps:
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple_product']);
        $this->clickButton('add_to_cart_by_sku');
        //Verifying
        $this->assertMessagePresent('success', 'product_added_to_cart_by_sku');
    }

    /**
     * <p>Adding to Cart by SKU after entering values in multiple fields</p>
     *
     * @param string $category
     * @param array $data
     *
     * @test
     * @depends createCategory
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4102
     */
    public function addMultipleSimpleProducts($category, $data)
    {
        //Steps:
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        $this->clickButton('add_row_by_sku', false);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple_product'], array('1', '2'));
        $this->clickButton('add_to_cart_by_sku');
        //Verifying
        $this->addParameter('number', '2');
        $this->assertMessagePresent('success', 'products_added_to_cart_by_sku');
    }

    /**
     * <p>Adding to Cart by SKU after entering valid and invalid values in multiple fields</p>
     *
     * @param string $category
     * @param array $data
     *
     * @test
     * @depends createCategory
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4103
     */
    public function addMultipleSimpleProductsFailure($category, $data)
    {
        //Steps:
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);
        $this->clickButton('add_row_by_sku', false);
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(
            array('sku' => $data['simple_product']['sku'], 'qty' => '#$%'), array('1')
        );
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple_product'], array('2'));
        $this->clickButton('add_to_cart_by_sku', false);
        //Verifying
        $this->assertMessagePresent('error', 'sku_invalid_number');
    }
}
