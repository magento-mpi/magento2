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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Helper extends Mage_Selenium_TestCase
{
   /**
    * Generates array of strings for filling customer's billing/shipping form
    * @param string $charsType :alnum:, :alpha:, :digit:, :lower:, :upper:, :punct:
    * @param string $addrType Gets two values: 'billing' and 'shipping'.
    *                         Default is 'billing'
    * @param int $symNum min = 5, default value = 32
    * @return array
    * @uses DataGenerator::generate()
    * @see DataGenerator::generate()
    */
    public function customerAddressGenerator($charsType, $addrType = 'billing', $symNum = 32, $required = false)
    {
        $type = array (':alnum:', ':alpha:', ':digit:', ':lower:', ':upper:', ':punct:');
        if (array_search($charsType, $type) === false || ($addrType != 'billing'
                && $addrType != 'shipping') || $symNum < 5 || gettype($symNum) != 'integer')
        {
            throw new Exception('Incorrect parameters');
        } else {
        if ($required == true){
            return $data = array(
                $addrType.'_prefix' => '',
                $addrType.'_first_name' => $this->generate('string', $symNum, $charsType),
                $addrType.'_middle_name' => '',
                $addrType.'_last_name' => $this->generate('string', $symNum, $charsType),
                $addrType.'_suffix' => '',
                $addrType.'_company' => '',
                $addrType.'_street_address_1' => $this->generate('string', $symNum, $charsType),
                $addrType.'_street_address_2' => '',
                $addrType.'_region' => '',
                $addrType.'_city' => $this->generate('string', $symNum, $charsType),
                $addrType.'_zip_code' => $this->generate('string', $symNum, $charsType),
                $addrType.'_telephone' => $this->generate('string', $symNum, $charsType),
                $addrType.'_fax' => ''
            );
        } else {
            return $data = array(
            $addrType.'_prefix' => $this->generate('string', $symNum, $charsType),
            $addrType.'_first_name' => $this->generate('string', $symNum, $charsType),
            $addrType.'_middle_name' => $this->generate('string', $symNum, $charsType),
            $addrType.'_last_name' => $this->generate('string', $symNum, $charsType),
            $addrType.'_suffix' => $this->generate('string', $symNum, $charsType),
            $addrType.'_company' => $this->generate('string', $symNum, $charsType),
            $addrType.'_street_address_1' => $this->generate('string', $symNum, $charsType),
            $addrType.'_street_address_2' => $this->generate('string', $symNum, $charsType),
            $addrType.'_region' => $this->generate('string', $symNum, $charsType),
            $addrType.'_city' => $this->generate('string', $symNum, $charsType),
            $addrType.'_zip_code' => $this->generate('string', $symNum, $charsType),
            $addrType.'_telephone' => $this->generate('string', $symNum, $charsType),
            $addrType.'_fax' => $this->generate('string', $symNum, $charsType)
            );
        }
      }
    }
   /**
    * Creates order for existing cutomer
    *
    * @param array|string $productsToBuy Use array or dataset name
    * @param array|string $billForm
    * @param array|string $shipForm
    * @param array|string $customerEmail
    * @param string $storeName
    * @param array|string $paymentMethod
    * @param string $shippingMethod
    * @param bool $validate No need to put any parameters except this one and $storeName for messages validation
    *
    * @return bool|integer
    */
    public function createOrderForExistingCustomer($validate = false, $productsToBuy = null,
            $billForm = null, $shipForm = null,
            $customerEmail = null, $storeName = null,
            $paymentMethod = null, $shippingMethod = null)
    {
        $this->addParameter('id', '0');
        if ($customerEmail != null)
        {
            if ($billForm != null){
                $this->addParameter('storeName', $storeName);
                $this->getToCreateOrderPage($customerEmail);
                if (gettype($billForm) == 'array'){
                    if (array_key_exists('first_name', $billForm)){
                        $customer = $billForm['first_name'].
                            ' '.$billForm['last_name'].', '
                            .$billForm['street_address_line_1'].
                            ' '.$billForm['street_address_line_2'].', '
                            .$billForm['city'].', '.$billForm['zip_code'].
                            ', '.$billForm['country'];
                        $addrToSearch = array('billing_address_choice' => $customer);
                        $this->fillForm($addrToSearch);
                    }
                    if (array_key_exists('billing_first_name', $billForm)){
                        $userData = $this->loadData(
                            'new_customer_order_billing_address_reqfields');
                        $addrToChoose = array('billing_address_choice' => 'Add New Address');
                        $addrToFill = array_merge($userData, $addrToChoose,$billForm);
                        $this->fillForm($addrToFill);
                    }
                }
                if (gettype($billForm) == 'string'){
                    $addrToChoose = array('billing_address_choice' => 'Add New Address');
                    $addrToFill = $this->loadData($billForm, $addrToChoose);
                }
            }
            if ($shipForm != null){
                if (gettype($billForm) == 'array'){
                    if (array_key_exists('first_name', $shipForm)){
                        $customer = $shipForm['first_name'].
                            ' '.$shipForm['last_name'].', '
                            .$shipForm['street_address_line_1'].
                            ' '.$shipForm['street_address_line_2'].', '
                            .$shipForm['city'].', '.$shipForm['zip_code'].
                            ', '.$shipForm['country'];
                        $addrToSearch = array('shipping_same_as_billing_address' => 'no',
                            'shipping_address_choice' => $customer);
                        $this->fillForm($addrToSearch);
                    }
                    if (array_key_exists('shipping_first_name', $shipForm)){
                        $userData = $this->loadData(
                            'new_customer_order_shipping_address_reqfields');
                        $addrToChoose = array('shipping_same_as_billing_address' => 'no',
                            'shipping_address_choice' => 'Add New Address');
                        $addrToFill = array_merge($addrToChoose, $userData, $shipForm);
                        $this->fillForm($addrToFill);
                    }
                    if (gettype($shipForm) == 'string'){
                        $addrToChoose = array('shipping_address_choice' => 'Add New Address');
                        $addrToFill = $this->loadData($shipForm, $addrToChoose);
                    }
                }

            } else {
                $addrToSearch = array('shipping_same_as_billing_address' => 'yes');
                $this->fillForm($addrToSearch);
            }
            if ($productsToBuy != null){
                $this->orderProducts($productsToBuy);
            }
            if ($paymentMethod != null){
                $this->payForOrder($paymentMethod);
            }
            if ($shippingMethod != null){
                $this->shipOrder($shippingMethod);
            }
            if ($customerEmail != null){
                if (gettype($customerEmail) == 'string'){
                    $customerEmail = array('email'=> $customerEmail);
                }
                $this->fillForm($customerEmail, 'order_account_information');
            }
            if ($validate == true){
                $this->clickButton('submit_order', FALSE);
            } else {
                $this->clickButton('submit_order', TRUE);
                $this->defineId('view_order');
                if ($this->successMessage('success_created_order') == true){
                    $this->assertTrue($this->successMessage('success_created_order'),
                        $this->messages);
                    return $order_id = $this->defineOrderId('view_order');
                } else {
                    return false;
                }
            }
        }
    }
   /**
    * Creates order for new cutomer
    *
    * @param array|string $productsToBuy Use array or dataset name
    * @param array|string $billForm
    * @param array|string $shipForm
    * @param array|string $customerEmail
    * @param string $storeName
    * @param bool $saveBillToAddrBook
    * @param bool $saveShipToAddrBook
    * @param array|string $paymentMethod
    * @param string $shippingMethod
    * @param bool $validate No need to put any parameters except this one and $storeName for messages validation
    *
    * @return bool|integer
    */
    public function createOrderForNewCustomer($validate = false, $productsToBuy = null,
            $billForm = null, $shipForm = null,
            $customerEmail = null, $storeName = null,
            $saveBillToAddrBook = false, $saveShipToAddrBook = false,
            $paymentMethod = null, $shippingMethod = null)
    {
        $this->addParameter('id', '0');
        $this->addParameter('storeName', $storeName);
        $this->getToCreateOrderPage();
        if ($billForm == null){
            throw new Exception('You are using method incorrectly.
                $billForm cannot be null.');
        } else {
            if ($saveBillToAddrBook == true){
                $saveBillToAddrBookParam = array(
                    'billing_save_in_address_book' => 'yes');
                if (gettype($billForm) == 'string'){
                    $userData = $this->loadData($billForm);
                    $billAddr = array_merge(
                        $userData, $saveBillToAddrBookParam);
                }
                if (getType($billForm) == 'array'){
                    $userData = $this->loadData(
                        'new_customer_order_billing_address_reqfields');
                    $billAddr = array_merge(
                        $userData, $saveBillToAddrBookParam, $billForm);
                }
                $this->fillForm($billAddr);
            } else {
                if (gettype($billForm) == 'string'){
                    $billAddr = $this->loadData($billForm);
                }
                if (getType($billForm) == 'array'){
                    $userData = $this->loadData(
                        'new_customer_order_billing_address_reqfields');
                    $billAddr = array_merge($userData, $billForm);
                }
                $this->fillForm($billAddr);
                }
                if ($shipForm != null){
                    if ($saveShipToAddrBook == true){
                        $saveShipToAddrBookParam = array(
                            'shipping_save_in_address_book' => 'yes');
                        if (gettype($shipForm) == 'string'){
                            $userData = $this->loadData($shipForm);
                            $shipAddr = array_merge(
                                $userData, $saveShipToAddrBookParam);
                        }
                        if (getType($shipForm) == 'array'){
                            $userData = $this->loadData(
                                'new_customer_order_shipping_address_reqfields');
                            $shipAddr = array_merge(
                                $userData, $saveShipToAddrBookParam, $shipForm);
                        }
                        $this->fillForm($shipAddr);
                    } else {
                        if (gettype($shipForm) == 'string'){
                            $shipAddr = $this->loadData($shipForm);
                        }
                        if (getType($shipForm) == 'array'){
                            $userData = $this->loadData(
                                'new_customer_order_shipping_address_reqfields');
                        $shipAddr = array_merge($userData, $shipForm);
                        }
                        $this->fillForm($shipAddr);
                    }
                } else {
                    $shipAddrSameAsBill = array('shipping_same_as_billing_address' => 'yes');
                    $this->fillForm($shipAddrSameAsBill);
                }
                if ($productsToBuy != null){
                    $this->orderProducts($productsToBuy);
                }
                if ($paymentMethod != null){
                    $this->payForOrder($paymentMethod);
                }
                if ($shippingMethod != null)
                {
                    $this->shipOrder($shippingMethod);
                }
                if ($customerEmail != null){
                    if (gettype($customerEmail) == 'string'){
                        $customerEmail = array('email'=> $customerEmail);
                    }
                    $this->fillForm($customerEmail, 'order_account_information');
                }
                if ($validate == true){
                    $this->clickButton('submit_order', FALSE);
                } else {
                $this->clickButton('submit_order', TRUE);
                $this->defineId('view_order');
                $this->assertTrue($this->checkCurrentPage('view_order'),
                    'Wrong page is opened');
                if ($this->successMessage('success_created_order') == true){
                    $this->assertTrue($this->successMessage('success_created_order'),
                        $this->messages);
                    return $order_id = $this->defineOrderId('view_order');
                } else {
                    return false;
                }
            }
        }
    }
   /**
    * Creates product needed for creating order.
    *
    * @param string $dataSetName     Dataset name with product information needed for creation.
    *                                Non-empty SKU is obligatory.
    * @param bool $createNewIfLowQty This flag will force creation of the product
    *                                in case when the qty in stock is less than 10.
    *                                The previous product will be deleted.
    */
    public function createProducts($dataSetName, $createNewIfLowQty = FALSE)
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'Wrong page is opened');
        $this->clickButton('reset_filter', false);
        $this->pleaseWait();
        $productData = $this->loadData($dataSetName);
        if ($createNewIfLowQty == false){
            if ($this->searchProduct(
                    array('product_sku' => $productData['general_sku'])) == false){
                $this->productHelper()->createProduct($productData);
                $this->assertTrue($this->successMessage('success_saved_product'),
                        $this->messages);
            }
        } else {
            if ($this->searchProduct(array('product_sku' => $productData['general_sku']),
                    'product_grid') == false){
                $this->productHelper()->createProduct($productData);
                $this->assertTrue($this->successMessage('success_saved_product'),
                        $this->messages);
            } else {
                if ($this->searchProduct(
                        array('product_sku' => $productData['general_sku'],
                            'product_qty_to' => '10')) == true){
                    $this->searchAndOpen(
                            array('product_sku' => $productData['general_sku']), TRUE);
                    $this->assertTrue($this->checkCurrentPage('edit_product'),
                            'Wrong page is opened');
                    $this->assertTrue($this->defineId('edit_product'));
                    $this->deleteElement('delete', 'confirmation_for_delete');
                    $this->productHelper()->createProduct($productData);
                    $this->assertTrue($this->successMessage('success_saved_product'),
                            $this->messages);
                }
            }
        }
    }
   /**
    * Covering up traces. Able to remove/cancel order and remove customer.
    *
    * @param integer $orderId   Order Id to Cancel
    * @param array $customerEmail   Customer Email that should be removed
    */
    public function coverUpTraces($orderId = null, $customerEmail = null)
    {
        if ($orderId != null){
            $this->assertTrue($this->cancelOrders($orderId), 'Could not cancel order');
        }
        if ($customerEmail != null){
            $this->assertTrue($this->deleteCreatedUsers($customerEmail),
                    'Could not delete customer');
        }
    }
   /**
    * Defines page's id
    */
    public function defineId()
    {
        // ID definition
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
    }
   /**
    * Cancels orders. Used for covering up traces after testing
    *
    * @param integer $orderId Create Order returns OrderID.
    *                         Pass this parameter from there or anyway you like
    * @return bool  Returns false if the order was not canceled, true if order
    *               was canceled successfully.
    */
    private function cancelOrders($orderId)
    {
        $this->assertTrue($this->navigate('manage_sales_orders'),
                'Could not get to Manage Sales Orders page');
        $arg = array(1 => $orderId);
        $this->searchAndChoose($arg, 'sales_order_grid');
        $userData = array ('actions' => 'Cancel');
        $this->fillForm($userData, 'sales_order_grid');
        $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        $this->assertTrue($this->clickButton('submit'), 'Could not press button Submit');
        if ($this->successMessage('success_canceled_order') == true){
            $this->assertTrue($this->successMessage('success_canceled_order'),
                    $this->messages);
            return true;
        } else {
            return false;
        }
    }
   /**
    * Deletes customers account. Used for covering up traces after testing
    *
    * @param string $customerEmail  Customer email is unique value in customers account.
    *                               Pass the same parameter as during order for new customer creation
    *                               or customer creation from 'manage_customers' page
    * @return bool                  Returns false if the customer was not deleted,
    *                               true if the customer's account was deleted successfully.
    */
    private function deleteCreatedUsers($customerEmail)
    {
        $this->assertTrue($this->navigate('manage_customers'));
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'Wrong page is opened');
        $this->addParameter('id', '0');
        $this->CustomerHelper()->openCustomer($customerEmail);
        $this->deleteElement('delete_customer', 'confirmation_for_delete');
        if ($this->successMessage('success_deleted_customer') == true){
            $this->assertTrue($this->successMessage('success_deleted_customer'),
                    $this->messages);
            return true;
        } else {
            return false;
        }
        $this->checkCurrentPage('manage_customers');
    }
   /**
    * Defines order id
    *
    * @param string $fieldset
    * @return bool|integer
    */
    private function defineOrderId($fieldset)
    {
        try{
        $item_id = 0;
        $title_arr = explode('/', $this->getTitle());
        $item_id = preg_replace("/^[^0-9]?(.*?)[^0-9]?$/i", "$1", $title_arr[0]);
        return $item_id;
        }
        catch(Exception $e){
            $this->_error = true;
            return FALSE;
        }
    }
   /**
    * Search for product
    *
    * @param array $data
    * @param string $fieldSetName
    * @return bool
    */
    private function searchProduct(array $data, $fieldSetName = null)
    {
        $this->_prepareDataForSearch($data);
        if (count($data) > 0) {
            if (isset($fieldSetName)) {
                $xpath = $this->getCurrentLocationUimapPage()->
                        findFieldset($fieldSetName)->getXpath();
            } else {
                $xpath = '';
            }
            $totalCount = intval($this->getText($xpath
                    . "//table[@class='actions']//td[@class='pager']//span[@id]"));
            $xpathTR = $xpath . "//table[@class='data']//tr";
            foreach ($data as $key => $value) {
                if (!preg_match('/_from/', $key) and !preg_match('/_to/', $key)) {
                    $xpathTR .= "[contains(.,'$value')]";
                }
            }
            if ($this->isElementPresent($xpathTR) && $totalCount > 0) {
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
   /**
    * Orders products during forming order
    * @param array|string $products Products in array or in dataset(string) to buy.
    */
    private function orderProducts($productsToBuy)
    {
        $this->clickButton('add_products', FALSE);
        $fieldsetName = 'select_products_to_add';
        if (gettype($productsToBuy) == 'array'){
            $products = $productsToBuy;
        }
        if (gettype($productsToBuy) == 'string'){
            $products = $this->loadData($productsToBuy);
        }else{
            throw new Exception('Incorrect type of $productsToBuy.');
        }
        foreach ($products as $key => $value){
            $prodToAdd = array($key => $value);
            $this->searchAndChoose($prodToAdd, $fieldsetName);
        }
        $this->clickButton('add_selected_products_to_order', FALSE);
        $this->pleaseWait();
    }
   /**
    * The way customer will pay for the order
    *
    * @param array|string $paymentMethod What type of credit card and credit cards' data to fill.
    */
    private function payForOrder($paymentMethod)
    {
        if (gettype($paymentMethod) == 'string'){
            $paymentMethod = $this->loadData($paymentMethod);
        }
        if (gettype($paymentMethod) != 'array'
                && gettype($paymentMethod) != 'string'){
                    throw new Exception('Incorrect type of $paymentMethod.');
        }
        $this->pleaseWait();
        $this->clickControl('radiobutton', 'credit_card', FALSE);
        $this->pleaseWait();
        $this->waitForAjax();
        $this->fillForm($paymentMethod, 'order_payment_method');
    }
   /**
    * The way to ship the order
    *
    * @param string $shippingMethod
    */
    private function shipOrder($shippingMethod)
    {
        if (gettype($shippingMethod) != 'string'){
            throw new Exception('Incorrect type of $shippingMethod.');
            }else{
                $this->clickControl('link', 'get_shipping_methods_and_rates',
                        FALSE);
                $this->pleaseWait();
                $this->addParameter('shipMethod', $shippingMethod);
                $this->clickControl('radiobutton', 'ship_method', FALSE);
                $this->pleaseWait();
                }
    }
   /**
    * Gets to 'Create new Order page'
    *
    * @param array|string $customerEmail Creates new order for new customer if the email is null.
    */
    private function getToCreateOrderPage($customerEmail = null)
    {
            $this->navigate('manage_sales_orders');
            $this->assertTrue($this->checkCurrentPage('manage_sales_orders'),
                    'Wrong page is opened');
            $this->assertTrue($this->clickButton('create_new_order', TRUE),
                'Could not press button "Add new" for creating new order');
            if ($customerEmail == null)
            {
                $this->assertTrue($this->clickButton('create_new_customer',
                        FALSE),
                    'Could not press button "Create new customer"
                        during creation of new order');
            } else {
                if (gettype($customerEmail) == 'string')
                {
                    $email = array ('email' => $customerEmail);
                }
                $this->searchAndOpen($email, FALSE);
            }
            //$this->addParameter('storeName', $storeName);
            if (($this->checkCurrentPage('create_order_for_new_customer') == TRUE)
                && ($this->controlIsPresent('radiobutton', 'choose_main_store'))) {
                    $this->assertTrue($this->clickControl('radiobutton',
                            'choose_main_store', FALSE),
                           'Could not choose main store during order creation');
                    $this->pleaseWait();
            }
    }
}
