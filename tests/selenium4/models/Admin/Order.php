<?php

/**
 * Admin_Admin_Order model
 *
 * @author Magento Inc.
 */
class Model_Admin_Order extends Model_Admin {

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $this->Data = array(
            'user_choise'               => '',
            'storeview_name'            => '',
            'gift_card_code'            => '',
            'coupon_code'               => '',
            'choise_billing_address'    => '',
            'choise_shipping_address'   => '',
            'payment_method'            => '',
            'shipping_method_title'     => '',
            'shipping_method'           => '',
        );
        $this->mapData = Core::getEnvMap('admin/pages/sales/orders/manage_orders/create_order');
    }

    /**
     * User’s choice for order creation.
     *
     * @param string $userType
     * has two values: 'new' и 'exist'.
     * If $userType='new' new user’s creation variant will be selected.
     * If $userType='esixt' existing user will be selected.
     * @param array $params
     * array in which values for user search are set (name, e-mail, telephone number etc.)
     */
    public function chooseUser($params, $userType)
    {
        $result = true;
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order/');
        $searchWord = '/^search_user/';
        $searchUser = $this->dataPreparation($params, $searchWord);
        switch ($userType) {
            case 'new':
                $this->click($this->getUiElement('buttons/new_customer'));
                $this->pleaseWait();
                break;
            case 'exist':
                $result = $this->searchAndDoAction('select_customer_container', $searchUser, 'open', NULL);
                if (!$result) {
                    $this->setVerificationErrors('Error searching customer');
                }
                break;
            default :
                $this->printInfo("\r\n The wrong type of user is given");
                $result = false;
        }
        return $result;
    }

    /**
     * Select Store.Before using it is needed to set UiNamespace.
     *
     * @param string $path
     * element name which contains Xpath element.
     * Variable is set on the basis of such a principle: UiNamespace/inputs/$path.
     * @param <type> $value
     * array element which contains Store name
     */
    public function selectStore($path, $value)
    {
        $result = TRUE;
        if (isset($value) and $value != Null) {
            $qtySite = $this->getXpathCount($this->getUiElement('inputs/' . $path, $value));
            if ($qtySite > 0) {
                if ($qtySite > 1) {
                    $this->printInfo("\r\n There are " . $qtySite . " elements for which value '" . $path . "'='" .
                            $value . "'. The first will be selected");
                }
                $this->click($this->getUiElement('inputs/' . $path, $value));
                $this->pleaseWait();
            } else {
                $this->setVerificationErrors("Element for which value '" . $path . "'='" . $value . "' does not exist");
                $result = FALSE;
            }
        } else {
            $this->setVerificationErrors("$path is not set");
            $result = FALSE;
        }
        return $result;
    }

    /**
     * Adding products for admin order creation.
     *
     * @param array $params
     * array which contains products identificators (sku, name, id etc.)
     * on which search for further adding will be done.
     */
    public function addProducts($params)
    {
        $result = TRUE;
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order');
        $searchWord = '/^search_product/';
        $searchProducts = $this->dataPreparation($params, $searchWord);
        if (count($searchProducts) > 0) {
            $this->click($this->getUiElement('buttons/add_product'));
            $this->multiRunSearch('select_product_for_order_container', $searchProducts, NULL);
            $this->click($this->getUiElement('buttons/add_product_confirm'));
            $this->pleaseWait();
        }
        if ($this->isElementPresent("//*[@id='order-items_grid']//tfoot")) {
            $arInfo = array();
            for ($i = 1; $i <= 5; $i++) {
                $arInfo[] = $this->getText($this->getUiElement('elements/order_items_info', $i));
            }
            $this->printInfo("\r\n After adding products:\r\n " . $arInfo[0] . " added.\r\n " . $arInfo[1] .
                    " " . $arInfo[2] . "\r\n Discount: " . $arInfo[3] . "\r\n Row Subtotal: " . $arInfo[4]);
            if ($this->isElementPresent($this->getUiElement('elements/error_for_added_product'))) {
                $qtyError = $this->getXpathCount($this->getUiElement('elements/error_for_added_product'));
                for ($i = 1; $i <= $qtyError; $i++) {
                    $error = $this->getText($this->getUiElement('elements/error_for_added_product') . "[$i]");
                    $productSKU = $this->getText($this->getUiElement('elements/error_for_added_product') . "[$i]" .
                                    $this->getUiElement('elements/sku_for_added_product'));
                    $this->printInfo("\r\n Product with $productSKU contains error '" . $error . "'");
                }
                $result = FALSE;
            }
        }
        return $result;
    }

    /**
     * Account Information Form. Fill in fields 'Email' and 'Group'
     *
     * @param array $params
     */
    public function newAccountInfo($params)
    {
        $result = $this->checkAndSelectField($params, 'user_group', Null);
        if ($result) {
            $this->pleaseWait();
        }
        $this->checkAndFillField($params, 'user_email', NULL);
    }

    /**
     * New data input in fields for Shipping or Billing address
     *
     * @param array $adress
     * array which contains data for Shipping or Billing address (First Name, Last Name, Country etc.)
     */
    public function newAddress($adress)
    {
        if (count($adress) > 0) {
            $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order');
            foreach ($adress as $key => $value) {
                if (preg_match('/country/', $key)) {
                    if (!$this->isElementPresent($this->getUiElement('selectors/' . $key) .
                                    $this->getUiElement('/admin/global/elements/selected_option', $value))) {
                        $this->select($this->getUiElement('selectors/' . $key), $value);
                        $this->pleaseWait();
                    }
                } elseif (preg_match('/state/', $key)) {
                    if (!$this->isElementPresent($this->getUiElement('inputs/not_active/' . $key))) {
                        $this->type($this->getUiElement('inputs/' . $key), $value);
                        if (preg_match('/shipping/', $key)) {
                            $this->pleaseWait();
                        }
                    } else {
                        $this->select($this->getUiElement('selectors/' . $key), $value);
                        if (preg_match('/shipping/', $key)) {
                            $this->pleaseWait();
                        }
                    }
                } elseif (preg_match('/save/', $key) and $value == 'Yes') {
                    $this->click($this->getUiElement('inputs/' . $key));
                    if (preg_match('/shipping/', $key)) {
                        $this->pleaseWait();
                    }
                } else {
                    $this->type($this->getUiElement('inputs/' . $key), $value);
                    if (preg_match('/shipping/', $key)) {
                        $this->pleaseWait();
                    }
                }
            }
        }
    }

    /**
     * Choice and filling in Shipping or Billing address
     *
     * @param string $tab
     * has two values: 'shipping' and  'billing' – defines which address will be fillen in
     * @param string $addressType
     * has four values: 'new','exist', 'default' and 'sameAsBilling'.
     * In accordance with these values a new address will be set or chosen existing one
     * or default address will be used. 'sameAsBilling' can be ised only for Shipping address.
     * @param array $params
     * array which contains data for Shipping or Billing address (First Name, Last Name, Country etc.)
     */
    public function fillAddressTab($params, $tab, $addressType)
    {
        $path = $this->getUiElement('selectors/select_' . $tab . '_address');
        switch ($tab) {
            case 'shipping':
                $searchWord = '/^shipping_(?!method)/';
                break;
            case 'billing':
                $searchWord = '/^billing_/';
                break;
        }
        $adress = $this->dataPreparation($params, $searchWord);
        switch ($addressType) {
            case 'new':
                if ($this->isElementPresent($path . $this->getUiElement('/admin/global/elements/selected_option', ''))) {
                    $this->select($path, 'label=Add New Address');
                    $this->pleaseWait();
                }
                $this->newAddress($adress);
                break;
            case 'exist':
                $addressCount = $this->getXpathCount($path . '/option');
                $res = 0;
                for ($i = 1; $i <= $addressCount; $i++) {
                    $addressValue = $this->getText($path . "/option[$i]");
                    foreach ($adress as $v) {
                        $res += preg_match("/$v/", $addressValue);
                    }
                    if ($res == count($adress)) {
                        $res = $addressValue;
                        break;
                    }
                    $res = 0;
                };
                if (is_string($res)) {
                    $this->select($path, 'label=' . $res);
                    $this->pleaseWait();
                    $this->printInfo($tab . " adress '" . $res . "' is selected");
                } else {
                    $this->printInfo("\r\n This address is not specified");
                }
                break;
            case 'default':
                if (!$this->isElementPresent($path . $this->getUiElement('/admin/global/elements/selected_option', ''))) {
                    $this->printInfo("\r\n Default address is not specified");
                }
                break;
            case 'sameAsBilling':
                if ($tab == 'shipping') {
                    $this->click($this->getUiElement('inputs/same_as_billing'));
                    $this->pleaseWait();
                } else {
                    $this->printInfo('This option can not be chosen for Billing address');
                }
                break;
            default :
                $this->printInfo("\r\n The wrong type of $tab address is given,  the default address will be used if it is set");
        }
    }

    /**
     * Select Shipping Method for order.
     *
     * @param string $shippingMethodTitle
     * title for Shipping Method which is set in System->Configuration->Shipping Methods-><Method>->Title
     * @param string $shippingMethod
     * Method Name which is set in System->Configuration->Shipping Methods-><Method>->Method Name
     * (for Flat Rate,Free Shipping) and in System->...->Allowed Methods (for UPS, USPS, FedEx,DHL)
     */
    public function selectShippingMethod($shippingMethodTitle, $shippingMethod)
    {
        $result = TRUE;
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order');
        $this->click($this->getUiElement('inputs/get_shipping_methods'));
        $this->pleaseWait();
        if ($this->isTextPresent($this->getUiElement('elements/no_shipping'))) {
            $this->printInfo("\r\n Shipping Method: " . $this->getUiElement('elements/no_shipping'));
            $result = FALSE;
        } elseif ($this->isElementPresent($this->getUiElement('elements/shipping_container')) and
                $shippingMethodTitle != Null and $shippingMethod != Null) {
            if ($this->isElementPresent($this->getUiElement('elements/one_shipping_container', $shippingMethodTitle) .
                            $this->getUiElement('inputs/allowed_method', $shippingMethod))) {
                $this->click($this->getUiElement('elements/one_shipping_container', $shippingMethodTitle) .
                        $this->getUiElement('inputs/allowed_method', $shippingMethod));
                $this->pleaseWait();
            } elseif ($this->isElementPresent($this->getUiElement('elements/shipping_container') .
                            $this->getUiElement('elements/one_shipping_container', $shippingMethodTitle) .
                            $this->getUiElement('/admin/messages/error'))) {
                $error = $this->getText($this->getUiElement('elements/shipping_container') .
                                $this->getUiElement('elements/one_shipping_container', $shippingMethodTitle) .
                                $this->getUiElement('/admin/messages/error'));
                $this->setVerificationErrors($error);
                $result = FALSE;
            } else {
                $this->setVerificationErrors('This shipping method is currently unavailable.');
                $result = FALSE;
            }
        } else {
            $this->printInfo('Shipping Method is not set');
            $result = FALSE;
        }
        if ($result) {
            $info = $this->getText($this->getUiElement('elements/selected_shipping_info_container'));
            $info = str_replace("\n Click to change shipping method", '', $info);
            $this->printInfo("\r\n Information about the selected Shipping method:\r\n " . $info);
        }
        return $result;
    }

    /**
     * Select Payment Method.
     *
     * @param string $paymentMethod
     * has 6 values: 'paypaluk_direct', 'verisign', 'paypal_direct', 'authorizenet', 'ccsave', 'money_order' -
     * each of which fits chosen Payment methodу
     * @param array $params
     */
    public function selectPaymentMethod($params, $paymentMethod)
    {
        $result = TRUE;
        $par = array();
        $mapData = $par ? $par : $this->mapData;
        $searchWord = '/^card_/';
        $cardData = $this->dataPreparation($params, $searchWord);
        if ($paymentMethod != NULL) {
            $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order');
            if ($this->isTextPresent($this->getUiElement('elements/no_payment'))) {
                $this->printInfo("\r\n Payment Method: " . $this->getUiElement('elements/no_payment'));
            } elseif ($this->isElementPresent($this->getUiElement('inputs/' . $paymentMethod))) {
                $this->click($this->getUiElement('inputs/' . $paymentMethod));
                $this->pleaseWait();
                if ($this->isElementPresent($this->getUiElement('elements/credit_card_payment', $paymentMethod))) {
                    if (count($cardData) > 0) {
                        foreach ($cardData as $key => $value) {
                            if (isset($mapData['inputs'][$key])) {
                                if ($this->isElementPresent($this->getUiElement('inputs/' . $key, $paymentMethod))) {
                                    $this->type($this->getUiElement('inputs/' . $key, $paymentMethod), $value);
                                }
                                if ($this->isTextPresent('Please wait...')) {
                                    $this->pleaseWait();
                                }
                            } elseif (isset($mapData['selectors'][$key])) {
                                if ($this->isElementPresent($this->getUiElement('selectors/' . $key, $paymentMethod))) {
                                    $this->select($this->getUiElement('selectors/' . $key, $paymentMethod),
                                            "label=regexp:" . $value);
                                }
                                if ($this->isTextPresent('Please wait...')) {
                                    $this->pleaseWait();
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $this->printInfo("\r\n Payment Method is not set");
        }
    }

    /**
     * Add discount code
     *
     * @param string $discountType
     * has two values: 'coupon' and 'gift_card'
     * @param string $code
     * @return boolean
     */
    public function addDiscount($discountType, $code)
    {
        $result = TRUE;
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order');
        if ($this->isElementPresent($this->getUiElement('inputs/' . $discountType . '_code')) and $code != NULL) {
            $this->type($this->getUiElement('inputs/' . $discountType . '_code'), $code);
            $this->click($this->getUiElement('buttons/add_' . $discountType));
            $this->pleaseWait();
            if ($this->isElementPresent($this->getUiElement('/admin/messages/error'))) {
                $etext = $this->getText($this->getUiElement('/admin/messages/error'));
                $this->setVerificationErrors($etext);
                $result = FALSE;
            } elseif ($this->isElementPresent($this->getUiElement('elements/' . $discountType . '_added', $code))) {
                $this->printInfo("\r\n $discountType with code=$code is added");
            }
        } elseif ($code != NULL) {
            $this->printInfo("\r\n There is no way to add $discountType code");
            $result = FALSE;
        }
        return $result;
    }

    /**
     * Adding Gift Message for order or product
     *
     * @param string $giftType
     * has two values: 'order' and  'product'
     * @param array $giftContent
     * array which contains field values 'To', 'From', 'Message' and if $giftType='product' - product name.
     */
    public function addGiftMessage($giftType, $giftContent)
    {
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order');
        $path = NULL;
        switch ($giftType) {
            case 'order':
                $giftMessageFor = $giftType;
                $path = $this->getUiElement('elements/gift_message_for_order_container');
                break;
            case 'product':
                if (isset($giftContent['product_gift_mes_product_name'])) {
                    $productName = $giftContent['product_gift_mes_product_name'];
                    $giftMessageFor = $productName . " $giftType";
                    if ($this->isElementPresent($this->getUiElement('elements/add_gift_mes_to_product', $productName))) {
                        $this->click($this->getUiElement('elements/add_gift_mes_to_product', $productName));
                        $this->click($this->getUiElement('buttons/update_items'));
                        $this->pleaseWait();
                        $path = $this->getUiElement('elements/gift_message_for_product_container', $productName);
                    } else {
                        $this->printInfo("\r\n You cannot add Gift Message for $giftMessageFor because this option is disabled");
                    }
                }
                break;
        }
        if ($this->isElementPresent($path) and $giftContent != Null and $path != NULL) {
            $ar = array();
            foreach ($giftContent as $k => $v) {
                if (preg_match('/_from$/', $k)) {
                    $ar['From'] = $v;
                }
                if (preg_match('/_to$/', $k)) {
                    $ar['To'] = $v;
                }
                if (preg_match('/_message$/', $k)) {
                    $ar['Message'] = $v;
                }
            }
            foreach ($ar as $key => $value) {
                ${'xpath' . $key} = $path . $this->getUiElement('inputs/gift_message_content', $key);
                $this->type(${'xpath' . $key}, $value);
            }
        } elseif ($giftContent != Null and $path != NULL) {
            $this->printInfo("\r\n You cannot add Gift Message for $giftMessageFor because this option is disabled");
        }
    }

    /**
     * Adding several Gift Messages for diferent products
     *
     * @param array $productGiftContent
     * array which contains field values 'To', 'From', 'Message' and Product Name.
     *
     */
    public function addGiftMessageForProducts($params)
    {
        $searchWord = '/^product_gift_mes_/';
        $productGiftContent = $this->dataPreparation($params, $searchWord);
        $isArr = false;
        foreach ($productGiftContent as $key => $value) {
            $isArr = $isArr || is_array($value);
        }
        if ($isArr) {
            $i = 1;
            $qtyNewArrays = 0;
            foreach ($productGiftContent as $k => $v) {
                foreach ($v as $v1) {
                    if (count($v) > $qtyNewArrays) {
                        $qtyNewArrays = count($v);
                    }
                    ${'array' . $i}[$k] = $v1;
                    $i++;
                }
                $i = 1;
            }
            for ($y = 1; $y <= $qtyNewArrays; $y++) {
                $this->addGiftMessage('product', ${'array' . $y});
            }
        } else {
            $this->addGiftMessage('product', $productGiftContent);
        }
    }

    /**
     * 3D Secure Card Validation. Work only for HTTPS!!!!
     */
    public function secureCardValidation()
    {
        $result = true;
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order');
        $path = $this->getUiElement('elements/secure_card_valid_container');
        if ($this->isElementPresent($path)) {
            $this->click($path . $this->getUiElement('buttons/start_card_validation'));
            $this->pleaseWait();
            if ($this->isAlertPresent()) {
                $text = $this->getAlert();
                $this->printInfo("\r\n +3D Secure error: " . $text);
                $result = FALSE;
            }
            if ($result) {
                $this->waitForElement($path . $this->getUiElement('elements/secure_fraime'), 30);
                $path = $this->getUiElement('elements/secure_page');
                $this->waitForElement($path, 50);
                sleep(1);
                $verifCode = $this->getText($path . $this->getUiElement('elements/secure_code'));
                preg_match("/([0-9]+)/i", $verifCode, $score);
                $this->printInfo("\r\n 3D secure verification code is " . $score[1]);
                $this->type($path . $this->getUiElement('inputs/secure_validation_code'), $score[1]);
                $this->clickAndWait($path . $this->getUiElement('buttons/submit_card_validation'));
                if ($this->isElementPresent($path . $this->getUiElement('elements/secure_error'))) {
                    $text = "3D Secure error: Password" . $this->getText($path . $this->getUiElement('elements/secure_error'));
                } elseif ($this->waitForElement("//html/body/h1", 20)) {
                    $text = "3D Secure:" . $this->getText("//html/body/h1") . ". " . $this->getText("//html/body/p");
                } else {
                    $text = "3D Secure: No success message";
                }
                $this->printInfo("\r\n" . $text);
            }
        }
    }

    /**
     * Crete new order
     *
     * @param array $params
     */
    public function doCreateOrder($params)
    {
        $Data = $params ? $params : $this->Data;
        $searchWord = '/^order_gift_mes_/';
        $orderGiftContent = $this->dataPreparation($params, $searchWord);
        // Open Order Page
        $this->clickAndWait($this->getUiElement('/admin/topmenu/sales/orders'));
        // Create new Order
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders/');
        $this->clickAndWait($this->getUiElement('buttons/create_order'));
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order');
        //choose User
        if ($this->chooseUser($params, $this->isSetValue($params, 'user_choise'))) {
            //select Store
            if ($this->selectStore('storeview_name', $this->isSetValue($params, 'storeview_name'))) {
                //Account Information
                $this->newAccountInfo($params);
                //add Product(s)
                if ($this->addProducts($params)) {
                    //add coupon Discount
                    $res1 = $this->addDiscount('coupon', $this->isSetValue($params, 'coupon_code'));
                    //add gift_card Discount
                    $res2 = $this->addDiscount('gift_card', $this->isSetValue($params, 'gift_card_code'));
                    $result = $res1 && $res2;
                    if ($result) {
                        //fill billing Address Tab
                        $this->fillAddressTab($params, 'billing', $this->isSetValue($params, 'choise_billing_address'));
                        //fill shipping Address Tab
                        if (!$this->isElementPresent($this->getUiElement('elements/only_virtual_added'))) {
                            $this->fillAddressTab($params, 'shipping', $this->isSetValue($params, 'choise_shipping_address'));
                        }
                        //adding Gift Message for product(s)
                        $this->addGiftMessageForProducts($params);
                        //adding Gift Message for order
                        $this->addGiftMessage('order', $orderGiftContent);
                        //select Shipping Method
                        if (!$this->isElementPresent($this->getUiElement('elements/only_virtual_added'))) {
                            $this->selectShippingMethod($this->isSetValue($params, 'shipping_method_title'),
                                    $this->isSetValue($params, 'shipping_method'));
                        }
                        //select Payment Method
                        $this->selectPaymentMethod($params, $this->isSetValue($params, 'payment_method'));
                        $this->secureCardValidation();
                        // get order Info before submit
                        $orderTotalBefore = strrev($this->getText("//*[@id='order-totals']//tbody"));
                        $orderTotalBefore = strrev(preg_replace("/ (?=([0-9]+.[0-9]+))/", " \n", $orderTotalBefore));
                        $this->printInfo("\r\n Before placing an order:\r\n " . $orderTotalBefore);
                        $result = $this->saveAndVerifyForErrors();
                        // get order Info after submit
                        if ($result) {
                            $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order/');
                            $ordNum = $this->getText($this->getUiElement('elements/order_number'));
                            $ord = explode(" ", $ordNum);
                            $orderStatus = $this->getText("//*[@id='order_status']");
                            $orderTotalAfter = strrev($this->getText("//*[contains(@class,'order-totals')]"));
                            $orderTotalAfter = strrev(preg_replace("/ (?=([0-9]+.[0-9]+))/", " \n", $orderTotalAfter));
                            $this->printInfo("\r\n After placing an order:\r\n " . $orderTotalAfter);
                            $this->printInfo("\r\n Order number - " . $ord[2] . "\r\n Order Status - " . $orderStatus);
                            $searchOrder['search_order_id'] = $ord[2];
                            return $searchOrder;
                        }
                    }
                }
            }
        }
        return NULL;
    }

    /**
     * Open order and perform actions on it.
     *
     * @param array $searchOrder
     * array in which values are set for Order search (id, status, creation date etc.)
     * @param string $actionName
     * At the moment can have four values: 'create_invoice', 'create_shippment', 'create_credit_memo','reorder'
     * create_invoice – invoice creation. Creation of partial invoice is not realized.
     * create_shippment – shippment creation. Creation of partial shippment is not realized.
     * create_credit_memo – creation of credit memo. Creation of partial credit memo is not realized.
     * reorder – order recreation
     */
    public function openOrderAndDoAction($searchOrder, $actionName)
    {
        $this->printDebug("$actionName started");
        // Open Order Page
        $this->clickAndWait($this->getUiElement('/admin/topmenu/sales/orders'));
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders');
        $searchResult = $this->searchAndDoAction('order_grid', $searchOrder, 'open', NULL);
        if ($searchResult) {
            $this->setUiNamespace('admin/pages/sales/orders/manage_orders/view_order');
            if (!$this->waitForElement($this->getUiElement('buttons/' . $actionName), 10)) {
                $this->printInfo("\r\n You cannot perform an action: $actionName");
                $result = false;
            } else {
                $this->clickAndWait($this->getUiElement('buttons/' . $actionName));
                $saveResult = $this->saveAndVerifyForErrors();
                if ($saveResult) {
                    $orderStatus = $this->getText("//*[@id='order_status']");
                    $orderTotalAfter = strrev($this->getText("//*[contains(@class,'order-totals')]"));
                    $orderTotalAfter = strrev(preg_replace("/ (?=([0-9]+.[0-9]+))/", " \n", $orderTotalAfter));
                    $this->printInfo("\r\n After $actionName:\r\n " . $orderTotalAfter);
                    $this->printInfo("\r\n Order Status - " . $orderStatus);
                }
            }
        } else {
            $this->printInfo("\r\n You cannot perform an action: $actionName because the order is not specified");
        }
        $this->printDebug("$actionName finished");
    }

}