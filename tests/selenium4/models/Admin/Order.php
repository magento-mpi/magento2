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
            'billing_address_choise'    => '',
            'shipping_address_choise'   => '',
            'payment_method'            => '',
            'shipping_method_title'     => '',
            'shipping_method'           => ''
        );
    }

    /**
     * Выбор пользователя для создания заказа.
     *
     * @param string $userType
     * имеет два значения 'new' и 'exist'.
     * Если $userType='new' будет выбран вариант создания нового пользователя
     * Если $userType='esixt' то будет выбран существующих пользователь
     *
     * @param array $searchUser
     * массив в котором заданны значения для поиска пользователя (имя, e-mail, телефон и т.д.)
     */
    public function chooseUser($userType, $searchUser)
    {
        $result = true;
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order/');
        switch ($userType) {
            case "new":
                $this->click($this->getUiElement("buttons/new_customer"));
                $this->pleaseWait();
                break;
            case "exist":
                $result = $this->searchAndDoAction("select_customer_container", $searchUser, "open", NULL);
                if ($result) {
                    $this->pleaseWait();
                }
                break;
            default :
                $this->printInfo("\r\n The wrong type of user is given");
                $result = false;
        }
        return $result;
    }

    /**
     * Select Store.
     *
     * @param string $path
     * Название элемента в котором содержится Xpath элемента.
     * Задание идет по такому принципу: UiNamespace/inputs/$path.
     * Перед использованием нужно задавать UiNamespace.
     *
     * @param <type> $value
     * Элемент массива который содержит Store name
     */
    public function selectStore($path, $value)
    {
        $result = TRUE;
        if (isset($value) and $value != Null) {
            $qtySite = $this->getXpathCount($this->getUiElement("inputs/" . $path, $value));
            if ($qtySite > 0) {
                if ($qtySite > 1) {
                    $this->printInfo("\r\n There are " . $qtySite . " elements for which value '" . $path . "'='" . $value . "'. The first will be selected");
                }
                $this->click($this->getUiElement("inputs/" . $path, $value));
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
     * Добавление продуктов для создания ордера с админки
     *
     * @param array $searchProducts
     * Массив, который содержит идентификаторы продуктов(sku, name, id и т.д.)
     * по которым будет выполняться поиск для дальнейшего добавления.
     */
    public function addProducts($searchProducts)
    {
        $result = TRUE;
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order/');
        $this->click($this->getUiElement("buttons/add_product"));
        if (is_array($searchProducts) and count($searchProducts) > 0) {
            $isArr = false;
            foreach ($searchProducts as $key => $value) {
                $isArr = $isArr || is_array($value);
            }
            if ($isArr) {
                $i = 1;
                $qtyNewArrays = 0;
                foreach ($searchProducts as $k => $v) {
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
                    $this->searchAndDoAction("select_product_for_order_container", ${'array' . $y}, "mark", NULL);
                }
            } else {
                $this->searchAndDoAction("select_product_for_order_container", $searchProducts, "mark", NULL);
            }
        }
        $this->click($this->getUiElement("buttons/add_product_confirm"));
        $this->pleaseWait();
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
     * Заполнение полей для Shipping или Billing адресса
     *
     * @param array $adress
     * Массив, который содержит данные для Shipping или Billing адресса
     * (First Name, Last Name, Country и т.д.)
     */
    public function newAddress($adress)
    {
        if (count($adress) > 0 and is_array($adress)) {
            $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order');
            foreach ($adress as $key => $value) {
                if (preg_match('/country/', $key)) {
                    if (!$this->isElementPresent($this->getUiElement('selectors/' . $key) .
                                    $this->getUiElement('/admin/elements/selected_option', $value))) {
                        $this->select($this->getUiElement('selectors/' . $key), $value);
                        $this->pleaseWait();
                    }
                } elseif (preg_match('/state/', $key)) {
                    if (!$this->isElementPresent($this->getUiElement('inputs/not_active/' . $key))) {
                        $this->type($this->getUiElement('inputs/' . $key), $value);
                    } else {
                        $this->select($this->getUiElement('selectors/' . $key), $value);
                        $this->pleaseWait();
                    }
                } else {
                    $this->type($this->getUiElement("inputs/" . $key), $value);
                    if (preg_match('/shipping/', $key)) {
                        $this->pleaseWait();
                    }
                }
            }
        }
    }

    /**
     * Выбор Shipping или Billing адресса
     *
     * @param string $tab
     * Имеет 2 значения: 'shipping' и  'billing' - указывает какой адресс будет заполняться
     *
     * @param string $addressType
     * Имеет 4 значения: 'new','exist', 'default' и "sameAsBilling".
     * В соответствии с этими значениями будет создан новый адресс, выбран существующий
     * или же будет использоваться default адресс. "sameAsBilling" может использоваться только для
     * Shipping адресса.
     * 
     * @param array $adress
     * Массив, который содержит данные для Shipping или Billing адресса
     * (First Name, Last Name, Country и т.д.)
     */
    public function fillAddressTab($tab, $addressType, $adress)
    {
        switch ($addressType) {
            case 'new':
                if ($this->isElementPresent($this->getUiElement('selectors/select_' . $tab . '_address') .
                                $this->getUiElement('/admin/elements/selected_option', ""))) {
                    $this->select($this->getUiElement('selectors/select_' . $tab . '_address'), 'label=Add New Address');
                    $this->pleaseWait();
                }
                $this->newAddress($adress);
                break;
            case 'exist':
                $addressCount = $this->getXpathCount($this->getUiElement('selectors/select_' . $tab . '_address') . '/option');
                $res = 0;
                for ($i = 1; $i <= $addressCount; $i++) {
                    $addressValue = $this->getText($this->getUiElement('selectors/select_' . $tab . '_address') . "/option[$i]");
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
                    $this->select($this->getUiElement('selectors/select_' . $tab . '_address'), "label=" . $res);
                    $this->pleaseWait();
                    $this->printInfo($tab . " adress '" . $res . "' is selected");
                } else {
                    $this->printInfo("\r\n This address is not specified");
                }
                break;
            case 'default':
                if (!$this->isElementPresent($this->getUiElement('selectors/select_' . $tab . '_address') .
                                $this->getUiElement('/admin/elements/selected_option', ""))) {
                    $this->printInfo("\r\n Default address is not specified");
                }
                break;
            default :
                $this->printInfo("\r\n The wrong type of $tab address is given,  the default address will be used if it is set");
        }
    }

    /**
     * Выбор Shipping Method для оплаты заказа
     *
     * @param string $shippingMethodTitle
     * title для Shipping Method которое задается в System->Configuration->Shipping Methods-><Метод>->Title
     *
     * @param string $shippingMethod
     * Method Name которое задается в System->Configuration->Shipping Methods-><Метод>->Method Name
     * (для Flat Rate,Free Shipping) и в System->...->Allowed Methods (для UPS, USPS, FedEx,DHL)
     */
    public function selectShippingMethod($shippingMethodTitle, $shippingMethod)
    {
        $result = TRUE;
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order');
        $this->click($this->getUiElement('inputs/get_shipping_methods'));
        $this->pleaseWait();
        if ($this->isTextPresent($this->getUiElement('elements/no_shipping'))) {
            $this->setVerificationErrors($this->getUiElement('elements/no_shipping'));
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
                $this->setVerificationErrors("This shipping method is currently unavailable.");
                $result = FALSE;
            }
        } else {
            $this->printInfo("Shipping Method is not set");
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
     * Выбор Payment Method
     *
     * @param array $params
     */
    public function selectPaymentMethod($params)
    {
        $result = TRUE;
        $Data = $params ? $params : $this->Data;
        if ($Data['payment_method'] != NULL) {
            $paymentMethod = $Data['payment_method'];
            $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order');
            if ($this->isTextPresent($this->getUiElement('elements/no_payment'))) {
                $this->printInfo("\r\n " . $this->getUiElement('elements/no_payment'));
            } elseif ($this->isElementPresent($this->getUiElement('inputs/' . $paymentMethod))) {
                $this->click($this->getUiElement('inputs/' . $paymentMethod));
                $this->pleaseWait();
                if ($this->isElementPresent($this->getUiElement('elements/credit_card_payment', $paymentMethod))) {
                    if ($paymentMethod == 'ccsave') {
                        $cardName = $Data['card_name'];
                        $this->type($this->getUiElement("inputs/card_name"), $cardName);
                    }
                    if ($Data['card_type'] != NULL) {
                        $this->select($this->getUiElement("selectors/card_type", $paymentMethod), "label=" . $Data['card_type']);
                    }
                    if ($Data['card_number'] != NULL) {
                        $this->type($this->getUiElement("inputs/card_number", $paymentMethod), $Data['card_number']);
                        $this->pleaseWait();
                    }
                    if ($Data['card_month'] != NULL) {
                        $this->select($this->getUiElement("selectors/month_expiration", $paymentMethod), "label=regexp:" . $Data['card_month']);
                        $this->pleaseWait();
                    }
                    if ($Data['card_year'] != NULL) {
                        $this->select($this->getUiElement("selectors/year_expiration", $paymentMethod), "label=" . $Data['card_year']);
                        $this->pleaseWait();
                    }
                    if ($this->isElementPresent($this->getUiElement("inputs/verification_number", $paymentMethod))) {
                        if ($Data['card_verif_number'] != NULL) {
                            $this->type($this->getUiElement("inputs/verification_number", $paymentMethod), $Data['card_verif_number']);
                            $this->pleaseWait();
                        }
                    }
                }
            }
        } else {
            $this->printInfo("\r\n Payment Method is not set");
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
        foreach ($Data as $key => $value) {
            if (preg_match('/search_product/', $key)) {
                $searchProducts[$key] = $value;
            }
            if (preg_match('/search_user/', $key)) {
                $searchUser[$key] = $value;
            }
            if (preg_match('/billing_/', $key) and !preg_match('/choise/', $key)) {
                $billingData[$key] = $value;
            }
            if (preg_match('/shipping_/', $key) and !preg_match('/method/', $key) and !preg_match('/choise/', $key)) {
                $shippingData[$key] = $value;
            }
        }
        if (!isset($searchUser)) {
            $searchUser = NULL;
        }
        if (!isset($searchProducts)) {
            $searchProducts = NULL;
        }
        if (!isset($billingData)) {
            $billingData = NULL;
        }
        if (!isset($shippingData)) {
            $shippingData = NULL;
        }
        // Open Order Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/orders"));
        // Create new Order
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders/');
        $this->clickAndWait($this->getUiElement("buttons/create_order"));
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders/create_order');
        if ($this->chooseUser($Data['user_choise'], $searchUser)) {
            if ($this->selectStore("storeview_name", $Data["storeview_name"])) {
                if ($this->addProducts($searchProducts)) {
                    if (!$this->isElementPresent($this->getUiElement("elements/only_virtual_added"))) {
                        $this->fillAddressTab("billing", $Data['billing_address_choise'], $billingData);
                    }
                    $this->fillAddressTab("shipping", $Data['shipping_address_choise'], $shippingData);
                    if (!$this->isElementPresent($this->getUiElement("elements/only_virtual_added"))) {
                        $this->selectShippingMethod($Data['shipping_method_title'], $Data['shipping_method']);
                    }
                    $this->selectPaymentMethod($params);
                    $orderTotalBefore = $this->getText("//*[@id='order-totals']//tbody");
                    $orderTotalBefore = str_replace("0 ", "0\n ", $orderTotalBefore);
                    $this->printInfo("\r\n Before placing an order:\r\n " . $orderTotalBefore);
                    $result = $this->saveAndVerifyForErrors("containers");
                    //Definition of order number
                    if ($result) {
                        $ordNum = $this->getText($this->getUiElement("elements/order_number"));
                        $ord = explode(" ", $ordNum);
                        $orderTotalAfter = $this->getText("//*[contains(@class,'order-totals')]");
                        $orderStatus = $this->getText("//*[@id='order_status']");
                        $orderTotalAfter = str_replace("0 ", "0\n ", $orderTotalAfter);
                        $this->printInfo("\r\n After placing an order:\r\n " . $orderTotalAfter);
                        $this->printInfo("\r\n Order number - " . $ord[2] . "\r\n Order Status - " . $orderStatus);
                        $searchOrder["search_order_id"] = $ord[2];
                        return $searchOrder;
                    }
                }
            }
        }
        return NULL;
    }

    /** Открытие Ордера и выполнение дествий над ним.
     *
     * @param array $searchOrder
     * Массив в котором заданны значения для поиска Order(id, status, дата создания и т.д.)
     * @param string $actionName
     * В данный момент может иметь 4 значения: "create_invoice", "create_shippment", "create_credit_memo","reorder"
     * create_invoice - создание инвойса. Создание частичного инвойса не реализовано.
     * create_shippment - создание шипмента. Создание частичного шипмента не реализовано.
     * create_credit_memo - создание кредит мемо. Создание частичного кредит мемо не реализовано.
     * reorder - пересоздание заказа
     */
    public function openOrderAndDoAction($searchOrder, $actionName)
    {
        $this->printDebug("$actionName started");
        // Open Order Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/sales/orders"));
        $this->setUiNamespace('admin/pages/sales/orders/manage_orders');
        $searchResult = $this->searchAndDoAction("order_grid", $searchOrder, "open", NULL);
        if ($searchResult) {
            $this->setUiNamespace('admin/pages/sales/orders/manage_orders/view_order');
            //checking: can create an Invoice?
            if (!$this->waitForElement($this->getUiElement("buttons/" . $actionName), 10)) {
                $this->printInfo("\r\n You cannot perform an action: $actionName");
                $result = false;
            } else {
                $this->clickAndWait($this->getUiElement("buttons/" . $actionName));
                $saveResult = $this->saveAndVerifyForErrors();
                if ($saveResult) {
                    $orderTotalAfter = $this->getText("//*[contains(@class,'order-totals')]");
                    $orderStatus = $this->getText("//*[@id='order_status']");
                    $orderTotalAfter = str_replace("0 ", "0\n ", $orderTotalAfter);
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