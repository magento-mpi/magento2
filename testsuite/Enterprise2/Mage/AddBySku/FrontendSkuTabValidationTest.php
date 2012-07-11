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
 * Tests for Order by SKU functionality on the Frontend in My Account
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_AddBySku_FrontendSkuTabValidationTest extends Mage_Selenium_TestCase
{    
    /**
     * <p>Enable Order By SKU functionality on Frontend</p>
     */
    public function setUpBeforeTests()
    {
        //Data
        $config = $this->loadDataSet('OrderBySkuSettings', 'add_by_sku_general_group');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }
    
    /**
     * <p>Creating Simple product and customer</p>
     *
     * @return array
     * @test
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

        return array('simple_product'   => array ('sku'      => $simple['general_sku'],
                                                  'qty'      => 1),
                     'customer'         => array ('email'    => $userData['email'],
                                                  'password' => $userData['password']));
    }
            
    /**
     * <p>Valid values for QTY field according SRS</p>
     * <p>Preconditions:</p>
     *  <p>1. System - Configuration - SALES - Sales - Order by SKU Settings: Enable Order by SKU on My Account in Front-end - Yes, for Everyone.</p>
     *  <p>2. Simple product is created.</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Login to Front-end</p>
     *  <p>2. My Account - Order by SKU</p>
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
     * @param array $data
     *
     * @test
     * @dataProvider qtyListDataProvider 
     * @depends preconditionsForTests   
     * @TestlinkId TL-MAGE-3933, TL-MAGE-3889
     */
    public function qtyValidation($qty, $message, $data)
    { 
        //Preconditions:    
        $this->frontend();
        if ($this->controlIsPresent('link', 'log_in'))
            $this->customerHelper()->frontLoginCustomer($data['customer']);
        //Steps:
        $this->navigate('order_by_sku');    
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
     *  <p>1. System - Configuration - SALES - Sales - Order by SKU Settings: Enable Order by SKU on My Account in Front-end - Yes, for Everyone.</p>
     *  <p>2. Simple product is created.</p>
     *  <p>3. Enable Order by SKU on My Account in Front-end - Yes, for Everyone </p> 
     * 
     * <p>Steps:</p>
     *  <p>1. Login to Front-end.</p>
     *  <p>2. Click My Account - Order by SKU</p>
     *  <p>3. Leave SKU field  empty and enter a valid value in Qty field.</p>
     *  <p>Click the "Add to Cart" button.</p>
     * 
     * <p>Expected results:</p>
     *  <p>1. The row should be ignored.</p>
     *  <p>2. Customer is redirected to Shopping cart.</p>
     *  <p>3. System displays message "You have not entered any product sku".</p>
     * 
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3889
     */
    public function addEmptyRowQtyFields($data)
    {  
        //Preconditions:
        $this->shoppingCartHelper()->frontClearShoppingCart();
        if ($this->controlIsPresent('link', 'log_in'))
            $this->customerHelper()->frontLoginCustomer($data['customer']);
        //Steps:
        $this->navigate('order_by_sku');        
        $this->clickButton('add_to_cart');        
        //Verifying
        $this->assertMessagePresent('error', 'no_product_added_by_sku');        
    }
    
    /**
     * <p>Validation rows, for which SKU and Qty values are empty</p>
     * <p>Preconditions:</p>
     *  <p>1. System - Configuration - SALES - Sales - Order by SKU Settings: Enable Order by SKU on My Account in Front-end - Yes, for Everyone.</p>
     *  <p>2. Simple product is created.</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Login to Front-end</p>
     *  <p>2. My Account - Order by SKU</p>
     *  <p>3. Click the "Add Row" button.</p>
     *  <p>4. Enter a valid SKU and Qty.</p>
     *  <p>5. Click the "Add to Cart" button.</p>
     * 
     * <p>Expected results:</p>
     * 	<p>1. Customer is redirected to Shopping Cart page.</p>
     *  <p>2. The row, for which SKU value is empty, should be ignore</p>
     * 
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3889
     */
    public function addSimpleProductWithEmptyRow($data)
    { 
        //Preconditions:
        $this->shoppingCartHelper()->frontClearShoppingCart();
        if ($this->controlIsPresent('link', 'log_in'))
            $this->customerHelper()->frontLoginCustomer($data['customer']);
        //Steps:
        $this->navigate('order_by_sku'); 
        $this->clickButton('add_row', false);  
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple_product']);        
        $this->clickButton('add_to_cart');        
        //Verifying
        $this->assertMessagePresent('success', 'product_added_to_cart_by_sku');       
    }
    
    /**
     * <p>Adding to Cart by SKU after entering valid and invalid values in multiple fields</p>
     * <p>Preconditions:</p>
     *  <p>1. System - Configuration - SALES - Sales - Order by SKU Settings: Enable Order by SKU on My Account in Front-end - Yes, for Everyone.</p>
     *  <p>2. Simple product is created.</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Login to Frontend</p>
     *  <p>2. My Account - Order by SKU</p>
     *  <p>3. Click "Add Row" several times</p>
     *  <p>4. Enter valid value to the SKU field and invalid to Qty field.</p>
     *  <p>5. Fill in the remaining lines the valid values</p>
     *  <p>6. Click Add to Cart button</p>
     * 
     *  <p>Expected results:</p>
     *  <p>1. The items should not be added to the cart.</p>
     *  <p>2. Customer should stay on Order by SKU tab</p>
     *  <p>3. Error messages should be appeared under Qty field.</p>
     * 
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests   
     * @depends qtyValidation
     * @TestlinkId TL-MAGE-4057
     */
    public function addMultipleSimpleProductsFailure($data)
    {  
        //Preconditions:
        $this->frontend();
        if ($this->controlIsPresent('link', 'log_in'))
            $this->customerHelper()->frontLoginCustomer($data['customer']);
        //Steps:
        $this->navigate('order_by_sku'); 
        $this->clickButton('add_row', false);     
        $this->addBySkuHelper()->frontFulfillSkuQtyRows(array ('sku' => $data['simple_product']['sku'], 
                                                          'qty' => '#$%'), array ('1'));
        $this->addBySkuHelper()->frontFulfillSkuQtyRows($data['simple_product'], array('2'));        
        $this->clickButton('add_to_cart',false);        
        //Verifying
        $this->assertMessagePresent('error', 'sku_invalid_number');      
    }
    
    /**
     * <p>Disable order by SKU on My Account for for customers unselected group</p>
     * <p>Preconditions:</p>
     *  <p>1. Customer is created and assigned to General customer group.</p>
     *  <p>2. System - Configuration - SALES - Sales - Order by SKU settings: Enable Order by SKU on My Account in Front-end - Yes, for Specified Customer Groups; Customer Groups - Retailer (e.g.)</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Login to Frontend as the user non assigned to Retailer group</p>
     *  <p>2. Click My Account</p>
     *  <p>3. Observe My Account tabs</p>
     * 
     * <p>Expected results:</p>
     *  <p>1. "Order by SKU" tab is not present</p>
     * 
     * @param array $data
     * 
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3878
     */
    public function orderBySkuForUnselectedCustomer($data)
    {    
        //Preconditions:
        $config = $this->loadDataSet('OrderBySkuSettings', 'add_by_sku_retailer_group');        
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);        
        //Steps:
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->navigate('customer_account');
        //Verifying
        $this->assertFalse($this->controlIsPresent('link','sku_tab'), 'There is "Order by SKU" tab on the page. ');
    } 
    
    /**
     * <p>Disable Order by SKU on My Account in Front-end</p>
     * <p>Preconditions:
     *  <p>1. System>Configuration>SALES>Sales>Order by SKU settings</p>
     *  <p>2. Enable Order by SKU on My Account in Front-end - No</p>
     * 
     * <p>Steps:</p>
     *  <p>1. Login to Front-end as the user non assigned to Retailer group</p>
     *  <p>2. Click My Account</p>
     *  <p>3. Observe My Account tabs</p>
     * 
     * <p>Expected results:</p>
     *  <p>1. "Order by SKU" tab is not present</p>
     * 
     * @param array $data
     * 
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3868
     */
    public function orderBySkuDisabled($data)
    {    
        //Preconditions:
        $config = $this->loadDataSet('OrderBySkuSettings', 'add_by_sku_disabled');        
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);        
        //Steps:
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->navigate('customer_account');
        //Verifying
        $this->assertFalse($this->controlIsPresent('link','sku_tab'), 'There is "Order by SKU" tab on the page. ');
    }
}