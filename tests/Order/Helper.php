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
 * Create order tests.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Helper extends Mage_Selenium_TestCase
{
   /**
    * Fills customer's billing form.
    *
    */
    public function fillNewBillForm(array $userData, $shipSameAsBill = TRUE, $orderSaveInAddressBook = TRUE)
    {
        $this->assertTrue($this->clickButton('create_new_order', TRUE),
                'Could not press button "Add new" for creating new order');
        $this->assertTrue($this->clickButton('create_new_customer', FALSE),
                'Could not press button "Create new customer" during creaton of new order');
        $this->addParameter('storeName', 'Default Store View');
        if (($this->checkCurrentPage('create_order_for_new_customer') == TRUE)
                && ($this->controlIsPresent('radiobutton', 'choose_main_store'))) {
                $this->assertTrue($this->clickControl('radiobutton', 'choose_main_store', FALSE),
                        'Could not choose main store during order creation');
                $this->pleaseWait();
        }
        $this->refresh();
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->assertTrue($this->defineId('create_order_for_new_customer'));
        $this->fillForm($userData, 'order_billing_address');
        if ($shipSameAsBill == TRUE){
            $this->assertTrue($this->clickControl('checkboxe', 'shipping_same_as_billing_address', FALSE),
                    'Could not set shipping address the same as billing');
            $this->pleaseWait();
        }
        if ($orderSaveInAddressBook == TRUE){
            $this->assertTrue($this->clickControl('checkboxe', 'billing_save_in_address_book', FALSE),
                    'Billing address will be saved to address book');
            $this->pleaseWait();
        }
    }
   /**
    * Fills customer's shipping form.
    *
    */
    public function fillNewShipForm(array $userData, $shipSameAsBill = FALSE, $orderSaveInAddressBook = TRUE)
    {
        if ($shipSameAsBill == FALSE){
           $this->assertTrue($this->clickControl('checkboxe', 'shipping_same_as_billing_address', FALSE),
                   'Shipping address could not be set the same as billing');
            $this->pleaseWait();
        }
        $this->fillForm($userData, 'order_shipping_address');
        if ($orderSaveInAddressBook == TRUE){
            $this->assertTrue($this->clickControl('checkboxe', 'billing_save_in_address_book', FALSE),
                    'Shipping address could not be saved to address book');
            $this->pleaseWait();
        }
    }
   /**
    * Cancels pending orders.
    *
    */
    public function cancelPendingOrders($searchParam)
    {
        $this->assertTrue($this->navigate('manage_sales_orders'), 'Could not get to Manage Sales Orders page');
        $arg = array(1 => $searchParam, 2 => 'Pending');
        $this->searchAndChoose($arg, 'sales_order_grid');
        $userData = array ('actions' => 'Cancel');
        $this->fillForm($userData, 'sales_order_grid');
        $this->assertTrue($this->defineId('manage_sales_orders'));
        $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        $this->assertTrue($this->clickButton('submit'), 'Could not press button Submit');
        $this->assertTrue($this->successMessage('success_canceled_order'), $this->messages);
    }
   /**
    * Deletes created users.
    *
    */
    public function deleteCreatedUsers($customerDelete)
    {
        $this->assertTrue($this->navigate('manage_customers'));
        $this->assertTrue($this->checkCurrentPage('manage_customers'), 'Wrong page is opened');
        $this->addParameter('id', '0');
        $this->CustomerHelper()->openCustomer($customerDelete);
        $this->deleteElement('delete_customer', 'confirmation_for_delete');
        $this->assertTrue($this->successMessage('success_deleted_customer'), $this->messages);
        $this->checkCurrentPage('manage_customers');
    }
   /**
    * Defines Id from URL.
    *
    */
    public function defineId($fieldset)
    {
        // ID definition
        try{
        $item_id = 0;
        $title_arr = explode('/', $this->getLocation());
        $title_arr = array_reverse($title_arr);
        foreach ($title_arr as $key => $value) {
            if (preg_match('/id$/', $value) && isset($title_arr[$key - 1])) {
                $item_id = $title_arr[$key - 1];
                break;
            }
        }
        if ($item_id > 0) {
            $this->addParameter('id', $item_id);
        }
        $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        $this->checkCurrentPage($fieldset);
        return TRUE;
        }
        catch(Exception $e){
            $this->_error = true;
            return FALSE;
        }
    }
   /**
    * Creates product needed for creating order.
    *
    */
    public function createProducts($dataSetName, $createNewIfExists = FALSE)
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        //Generating unique sku for each product.
        $sku = $this->generate('string', 6, ':lower:');
        $productData = $this->loadData($dataSetName, array('general_sku' => $sku));
        if ($createNewIfExists == false){
            if ($this->searchProduct(array('name' => $productData['general_name'])) == false){
                $this->productHelper()->createProduct($productData);
            }
        } else {
            $this->productHelper()->createProduct($productData);
        }
        $this->assertTrue($this->defineId('manage_products'));
    }
    //Got it from TestCase.php , but modified to return false, not to fail the test case.
    private function searchProduct(array $data, $fieldSetName = null)
    {
        $this->_prepareDataForSearch($data);
        if (count($data) > 0) {
            if (isset($fieldSetName)) {
                $xpath = $this->getCurrentLocationUimapPage()->findFieldset($fieldSetName)->getXpath();
            } else {
                $xpath = '';
            }
            //Forming xpath that contains string 'Total $number records found'
            //where $number - number of items in a table
            $totalCount = intval($this->getText($xpath
                    . "//table[@class='actions']//td[@class='pager']//span[@id]"));
            // Forming xpath for string that contains the lookup data
            $xpathTR = $xpath . "//table[@class='data']//tr";
            foreach ($data as $key => $value) {
                if (!preg_match('/_from/', $key) and !preg_match('/_to/', $key)) {
                    $xpathTR .= "[contains(.,'$value')]";
                }
            }
            if (!$this->isElementPresent($xpathTR) && $totalCount > 0) {
                // Fill in search form and click 'Search' button
                $this->fillForm($data);
                $this->clickButton('search', false);
                $this->pleaseWait();
            } elseif ($totalCount == 0) {
                return false;
            }
            if ($this->isElementPresent($xpathTR)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }
}
