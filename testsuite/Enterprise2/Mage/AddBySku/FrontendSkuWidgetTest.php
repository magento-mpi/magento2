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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tests for Order by SKU functionality on the Frontend via widget
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_AddBySku_FrontendSkuWidgetTest extends Mage_Selenium_TestCase
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
        $this->cmsWidgetsHelper()->deleteWidget(array('filter_type'  => 'Order by SKU'));        
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
        $categoryData = $this->loadDataSet('Category','sub_category_required');
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

        return array('simple_product'           => array ('sku'      => $simple['general_sku'],
                                                          'qty'      => 1),
                     'customer'                 => array ('email'    => $userData['email'],
                                                          'password' => $userData['password']));
    }
    
    /**
     * <p>Create Order by SKU type widget with disable widget options</p>
     * <p>Preconditions:</p>
     *  <p>1. System - Configuration - SALES - Sales - Order by SKU settings: Enable Order by SKU on My Account in Front-end - Yes, for Everyone</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Login to Back-end</p>
     *  <p>2. CMS - Widgets - Add New Widget Instance</p>
     *  <p>3. Choose Order By SKU in Type field</p>
     *  <p>4. Choose enterprise/default in the Design Package/Theme field</p>
     *  <p>5. Click Continue button</p>
     *  <p>6. Enter any Widget Instance Title (Order by SKU e.g.)</p>
     *  <p>7. Select some Store View in Assign to Store Views</p>
     *  <p>8. Click Add Layout Update button</p>
     *  <p>9. Choose All Page (e. g.)</p>
     *  <p>10. Choose Left Column (f.e.)</p>
     *  <p>11. Click Widget Options</p>
     *  <p>12. Display to Store View as Link to Loading a Spreadsheet - No</p>
     *  <p>13. Click SAVE button</p>
     *  
     * <p>Expected results:</p>
     *  <p>1. New widget is created and display in Widget list</p> 
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
        $widgetData = $this->loadDataSet('OrderBySkuWidget','sku_widget_without_link');
        //Steps
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->createWidget($widgetData);
        //Verifying
        $this->assertMessagePresent('success', 'successfully_saved_widget');
        $this->clearInvalidedCache();
        
        return array ('filter_type'  => $widgetData['settings']['type'],
                      'filter_title' => $widgetData['frontend_properties']['widget_instance_title']);
    }
    
    /**
     * <p>Create Order by SKU type widget with disable widget options</p>
     * <p>Preconditions:</p>
     *  <p>1. System - Configuration - SALES - Sales - Order by SKU settings: Enable Order by SKU on My Account in Front-end - Yes, for Everyone</p>
     *  <p>2. Order by SKU widget without link is created.</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Login to Frontend</p>
     *  <p>2. Observe newly created widget</p>
     *  
     * <p>Expected results:</p>
     *  <p>1. Widget "Order by SKU" is displayed on the all page in left column on Frontend.</p>
     *  <p>2. Link is absent in widget</p>
     * 
     * @param string $category 
     * 
     * @test
     * @depends createCategory
     * @depends createSKUWidgetWithoutLink    
     * @TestlinkId TL-MAGE-3974
     */
    public function checkSkuWidgetWithoutLink($category)
    {       
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category);      
        //Verifying
        $this->assertFalse($this->controlIsPresent('link','sku_link'), 'There is a link on the Order by SKU widget. ');     
    }
    
    /**
     * <p>Deleting Widget of the Order by SKU type</p> 
     * <p>Preconditions:</p> 
     *  <p>1. At least one widget Order by SKU type should be created.</p> 
     * 
     * <p>Steps:</p> 
     *  <p>1. Login to Back-end</p> 
     *  <p>2. CMS - Widgets - Select Order by SKU widget and click it.</p> 
     *  <p>3. Click "Delete" button</p> 
     *  <p>4. Click OK button</p> 
     * 
     * <p>Expected results:</p> 
     *  <p>1. Widget should be removed.</p> 
     *  <p>2. "The widget instance has been deleted" message should be displayed.</p> 
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
     * <p>Preconditions:</p> 
     *  <p>1. System - Configuration - SALES - Sales - Order by SKU settings: Enable Order by SKU on My Account in Front-end - Yes, for Everyone</p> 
     * 
     * <p>Steps:</p> 
     *  <p>1. Login to Back-end</p> 
     *  <p>2. CMS - Widgets - Add New Widget Instance</p> 
     *  <p>3. Choose Order By SKU in Type field</p> 
     *  <p>4. Choose enterprise/default in the Design Package/Theme field</p> 
     *  <p>5. Click Continue button</p> 
     *  <p>6. Enter any Widget Instance Title (Order by SKU e.g.)</p> 
     *  <p>7. Select some Store View in Assign to Store Views</p> 
     *  <p>8. Click Add Layout Update button</p> 
     *  <p>9. Choose All Page (e. g.)</p> 
     *  <p>10. Choose Left Column (f.e.)</p> 
     *  <p>11. Click Widget Options</p> 
     *  <p>12. Display to Store View as Link to Loading a Spreadsheet - Yes</p> 
     *  <p>13. Click SAVE button</p> 
     *  
     * <p>Expected results:</p> 
     *  <p>1. New widget is created and display in Widget list</p> 
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
        $widgetData = $this->loadDataSet('OrderBySkuWidget','sku_widget_with_link');
        //Steps
        $this->navigate('manage_cms_widgets');
        $this->cmsWidgetsHelper()->createWidget($widgetData);
        //Verifying
        $this->assertMessagePresent('success', 'successfully_saved_widget');
        $this->clearInvalidedCache();
        
        return array ('filter_type'  => $widgetData['settings']['type'],
                      'filter_title' => $widgetData['frontend_properties']['widget_instance_title']);
    }
    
    /**
     * <p>Create Order by SKU type widget with enable widget options</p>
     * <p>Preconditions:</p>
     *  <p>1. System - Configuration - SALES - Sales - Order by SKU settings: Enable Order by SKU on My Account in Front-end - Yes, for Everyone</p>
     *  <p>2. Order by SKU widget without link is created.</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Login to Front-end</p>
     *  <p>2. Observe newly created widget</p>
     *  
     * <p>Expected results:</p>
     *  <p>1. Widget "Order by SKU" is displayed on the all page in left column on Frontend.</p>
     *  <p>2. Load a list of SKUs link is present in widget</p>
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
        $this->assertTrue($this->controlIsPresent('link','sku_link'), 
                                                  'There is not a link on the Order by SKU widget. ');     
    }
    
    /**
     * <p>Displaying Link Text in the widget for everyone customer groups</p>
     * <p>Preconditions:</p>
     *  <p>1. At least one widget Order by SKU with enabled options should be created</p>
     *  <p>2. System - Configuration - SALES - Sales - Order by SKU settings: Enable Order by SKU on My Account in Front-end - Yes, for Everyone</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Login to Front-end</p>
     *  <p>2. Observe any widget Order by SKU type</p>
     *  <p>3. Enter valid values in SKU and QTY fields and Click  Load a list of SKUs link on widget</p>
     * 
     * <p>Expected results:</p>
     *  <p>1. Customer redirected to My Account > Order by SKU tab, values entered in SKU and Qty fields are not saved.</p>
     * 
     * @param string $category
     * @param array $data
     * 
     * @test
     * @depends createCategory
     * @depends preconditionsForTests
     * @depends checkSKUWidgetWithLink
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
        $this->assertTrue($this->checkCurrentPage('order_by_sku'),'This is not Order by SKU page');
        $this->assertTrue($this->getValue($this->_getControlXpath('field','sku')) == '',
                'SKU filed is not empty');
         $this->assertTrue($this->getValue($this->_getControlXpath('field','qty')) == '',
                'Qty filed is not empty');
    }
    
    /**
     * <p>Displaing Link Text in the widget for non logged in user</p>
     * <p>Preconditions:</p>
     *  <p>1. Order by SKU widget is created.</p>
     *  <p>2. On the Widget options  "Display a Link to Loading a Spreadsheet" - "Yes".</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Open Frontend as Guest</p>
     *  <p>2. Open page with "Order by SKU" widget.</p>
     *  <p>3. Click on the link "Load a list of SKUs"</p>
     * 
     * <p>Expected results:</p>
     *  <p>1. Guest should be redirected to "Login or Create an Account" page.</p>
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
     * <p>Preconditions:</p>
     *  <p>1. At least one widget Order by SKU type should be created.</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Login to Back-end</p>
     *  <p>2. CMS - Widgets - Select Order by SKU widget and click it.</p>
     *  <p>3. Navigate to Widget Options and edit Link Text (e.g.). Click Save button.</p>
     *  <p>4. Login to Frontend</p>
     *  <p>5. Open page with widget "Order by SKU" type</p>
     * 
     * <p>Expected results:</p>
     *  <p>1. Change the Link Text should be displayed on the  widget  in Front-end</p>
     * 
     * @param sring $category
     * @param array $selectWidget
     * 
     * @test
     * @depends createCategory
     * @depends createSKUWidgetWithLink
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
        $this->assertTrue($this->getText($this->_getControlXpath('link','sku_link')) == 
                $widgetOptions['sku_link_text'], 'The link on the Order by SKU widget is not changed. ');         
    }
    
    /**
     * <p>Displaying Link Text in the widget for specified customer group</p>
     * <p>Preconditions:</p>
     *  <p>1. At least one customers group must be created</p>
     *  <p>2. At least one customer must be assigned to this customer group</p>
     *  <p>3. At least one widget Order by SKU with enabled options  should be created</p>
     *  <p>4. System - Configuration - SALES - Sales - Order by SKU settings: Enable Order by SKU on My Account in Front-end - Yes, for Specified Customer Group, Customer Groups - General (e.g.)
     * 
     * <p>Steps:</p>
     *  <p>1. Login to Front-end as the user assigned to General group</p>
     *  <p>2. Navigate to widget and observe it</p>
     *  <p>3. Fill SKU and QTY fields valid values and click link</p>
     * 
     * <p>Expected results:</p>
     *  <p>1. Customer redirected to My Account - Order by SKU tab, values entered in SKU and Qty fields are not saved.</p>
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
        $config = $this->loadDataSet('OrderBySkuSetting', 'add_by_sku_general_group');        
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        //Steps
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->categoryHelper()->frontOpenCategory($category); 
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple_product']);
        $this->clickControl('link', 'sku_link');
        //Verifying
        $this->assertTrue($this->checkCurrentPage('order_by_sku'),'This is not Order by SKU page');
        $this->assertTrue($this->getValue($this->_getControlXpath('field','sku')) == '',
                'SKU filed is not empty');
         $this->assertTrue($this->getValue($this->_getControlXpath('field','qty')) == '',
                'Qty filed is not empty');
    }
    
    /**
     * <p>Displaying Link Text in the widget for specified customer group</p>
     * <p>Preconditions:</p>
     *  <p>1. At least one customers group must be created</p>
     *  <p>2. At least one customer must be assigned to this customer group</p>
     *  <p>3. At least one widget Order by SKU with enabled options  should be created</p>
     *  <p>4. System - Configuration - SALES - Sales - Order by SKU settings: Enable Order by SKU on My Account in Front-end - Yes, for Specified Customer Group, Customer Groups - Retailer (e.g.)</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Open frontend as the user assigned to unselected group</p>
     *  <p>2. Navigate to widget and observe it</p>
     * 
     * <p>Expected results:</p>
     *  <p>1. Link is not available on the widget</p>
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
        $config = $this->loadDataSet('OrderBySkuSetting', 'add_by_sku_retailer_group');        
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        //Steps
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->categoryHelper()->frontOpenCategory($category); 
        //Verifying
        $this->assertFalse($this->controlIsPresent('link','sku_link'), 
                                                   'The link on the Order by SKU widget should not be present. ');
    }
    /**
     * <p>Displaing Link Text in the widget with disabled SKU functionality on Frontend</p>
     * <p>Preconditions:</p>
     *  <p>1. At least one customers group must be created</p>
     *  <p>2. At least one customer must be assigned to this customer group</p>
     *  <p>3. At least one widget Order by SKU with enabled options  should be created</p>
     *  <p>4. System - Configuration - SALES - Sales>Order by SKU settings: Enable Order by SKU on My Account in Front-end - No</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Open frontend.</p>
     *  <p>2. Navigate to widget and observe it</p>
     * 
     * <p>Expected results:</p>
     *  <p>1.Link is not available on the widget</p>
     *
     * @param string $category
     * 
     * @test
     * @depends createCategory
     */
    public function widgetLinkForDisabledFrontendSku($category)
    {
        //Preconditions:
        $config = $this->loadDataSet('OrderBySkuSetting', 'add_by_sku_disabled');        
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        //Steps
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($category); 
        //Verifying
        $this->assertFalse($this->controlIsPresent('link','sku_link'), 
                                                   'The link on the Order by SKU widget should not be present. ');
    }
    
    /**
     * <p>Valid values for QTY field according SRS</p>
     * <p>Preconditions: </p>
     *  <p>1. At least one Widget Order by SKU type should be created</p>
     *  <p>2. Simple product is created.</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Go to Frontend</p>
     *  <p>2. Navigate to widget "Order by SKU" type </p>
     *  <p>3. Enter valid value in SKU field, enter non numeric value in QTY field and click Add to Cart button</p>
     *  <p>4. Enter valid value in SKU field, enter negative value in QTY field and click Add to Cart button</p>
     *  <p>5. Enter valid value in SKU field, enter 0 value in Qty field.</p>
     *  <p>6. Enter a valid value in SKU field, enter less than 0.0001 value in Qty field and click "Add to Cart" button</p>
     *  <p>7. Enter a valid value in SKU field, enter greater than 99999999.9999 value in Qty field and click "Add to Cart" button</p>
     *  <p>8. Enter a valid value in SKU field and leave Qty field empty.</p> 
     * 
     *  <p>Expected results:</p>
     *  <p>after step 3: Qty field is highlighted with red and "Please enter a valid number in this field." message is displayed under the field with "non numeric" value. Product is not added to Shopping Cart.
     *  <p>after step 4: QTY field is highlighted with red, "Please enter a number greater than 0 in this field" message is displayed, product is not added to Cart</p>
     *  <p>after step 5: Qty field is highlighted with red and "Please enter a number greater than 0 in this field." message is displayed under the field with "0" value. Product is not added to Shopping Cart.</p>
     *  <p>after step 6: Qty filed is highlighted with red and "The value is not within the specified range." message is displayed. Product is not added to Shopping Cart.</p>
     *  <p>after step 7: Qty filed is highlighted with red and "The value is not within the specified range." message is displayed. Product is not added to Shopping Cart.</p>
     *  <p>after step 8: This is a required field" message is displayed.</p>
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
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array ('sku' => $data['simple_product']['sku'], 'qty' => $qty));   
        $this->clickButton('add_to_cart', false);
        $this->waitForAjax();
        //Verifying
        $this->assertMessagePresent('error', $message);                  
    }
    
    public function qtyListDataProvider()
    {
        return array(
            array('non-num', 'sku_invalid_number'),
            array('-5', 'sku_negative_number'),
            array('0', 'sku_negative_number'),
            array('0.00001', 'sku_outofrange_number'),
            array('999999999.9999', 'sku_outofrange_number'),
            array('-5', 'sku_negative_number'),
            array('','sku_required_field')
        );
    }
    
    /**
     * <p>Validation rows, for which SKU and Qty values are empty</p>
     * <p>Preconditions:</p>
     *  <p>1. At least one Widget Order by SKU type should be created</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Go to Frontend</p>
     *  <p>2. Navigate to widget "Order by SKU" type</p>
     *  <p>3. Leave SKU field  empty and enter a valid value in Qty field.</p>
     *  <p>4. Click the "Add to Cart" button.</p>
     * 
     * <p>Expected results:</p>
     *  <p>1. The row should be ignored.</p>
     *  <p>2. Customer is redirected to Shopping cart.</p>
     *  <p>3. System displays message "You have not entered any product sku".</p>
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
        $this->clickButton('add_to_cart');        
        //Verifying
        $this->assertMessagePresent('error', 'no_product_added_from_widget');        
    }
    
    /**
     * <p>Validation rows, for which SKU and Qty values are empty</p>
     * <p>Preconditions:</p>
     *  <p>1. At least one Widget Order by SKU type should be created</p> 
     * 
     * <p>Steps:</p>
     *  <p>1. Go to Frontend</p>
     *  <p>2. Navigate to widget "Order by SKU" type</p>
     *  <p>3. Click the "Add Row" button.</p>
     *  <p>4. Enter a valid SKU and Qty.</p>
     *  <p>5. Click the "Add to Cart" button.</p>
     * 
     * <p>Expected results:</p>
     * 	<p>1. Customer is redirected to Shopping Cart page.</p>
     *  <p>2. The row, for which SKU value is empty, should be ignore</p>
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
        $this->clickButton('add_row', false);  
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple_product']);        
        $this->clickButton('add_to_cart');        
        //Verifying
        $this->assertMessagePresent('success', 'product_added_to_cart_by_sku');       
    }
    
    /**
     * <p>Adding to Cart by SKU after entering SKUs and Qtys manually</p>
     * <p>Preconditions:</p>
     *  <p>1. Order By SKU widget is created and placed to the Fronted</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Go to Frontend</p>
     *  <p>2. Open page with Order by Sku widget</p>
     *  <p>3. Enter valid values in SKU and QTY  fields for simple product</p>
     *  <p>4. Click Add to Cart Button</p>
     * 
     * <p>Expected results:</p>
     *  <p>1. SKU and Qty fields validation is performed without errors.</p>
     *  <p>2. Customer is redirected to the Shopping Cart.</p>
     *  <p>3. System displays message "n product was added to your shopping cart."</p>
     *  <p>4. Simple product is added to the Shopping Cart.</p>
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
        $this->clickButton('add_to_cart');        
        //Verifying
        $this->assertMessagePresent('success', 'product_added_to_cart_by_sku');       
    } 
    
    /**
     * <p>Adding to Cart by SKU after entering values in multiple fields</p>
     * <p>Preconditions:</p>
     *  <p>1. Order By SKU widget is created and placed to the Fronted</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Login to Front-end</p>
     *  <p>2. Open widget Order by SKU</p>
     *  <p>3. Click "Add Row" button several times.</p>
     *  <p>4. Enter valid values SKUs and QTYs and click Add to Cart button.</p>
     * 
     * <p>Expected results:</p>
     *  <p>1. All products, that  was entered in multiple fields, are added to Shopping Cart.</p>
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
        $this->clickButton('add_row', false);     
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple_product'], array ('1', '2'));        
        $this->clickButton('add_to_cart');        
        //Verifying
        $this->addParameter('number', '2');
        $this->assertMessagePresent('success', 'products_added_to_cart_by_sku');        
    }
    
    /**
     * <p>Adding to Cart by SKU after entering valid and invalid values in multiple fields</p>
     * <p>Preconditions:</p>
     *  <p>1. Order By SKU widget is created and placed to the Fronted</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Go to Frontend</p>
     *  <p>2. Open page with Order by Sku widget</p>
     *  <p>3. Click "Add Row" several times</p>
     *  <p>4. Enter valid value to the SKU filed and invalid to Qty field.</p>
     *  <p>5. Fill in the remaining lines the valid values</p>
     *  <p>6. Click Add to Cart button</p>
     * 
     * <p>Expected results:</p>
     *  <p>1. The items should not be added to the cart.</p>
     *  <p>2. Customer should stay on Order by SKU tab</p>
     *  <p>3. Error messages should be appeared under Qty field.</p>
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
        $this->clickButton('add_row', false);     
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array ('sku' => $data['simple_product']['sku'], 
                                                          'qty' => '#$%'), array ('1'));
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple_product'], array('2'));        
        $this->clickButton('add_to_cart',false);        
        //Verifying
        $this->assertMessagePresent('error', 'sku_invalid_number');      
    }
}