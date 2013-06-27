<?php
/**
 * Helper class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Core_Mage_Product_Helper extends Mage_Selenium_AbstractHelper
{
    public $productTabs = array('images', 'meta_information', 'prices', 'design', 'autosettings', 'websites',
        'related', 'up_sells', 'cross_sells', 'custom_options', 'downloadable_information', 'general', 'inventory');

    #**************************************************************************************
    #*                                                    Frontend Helper Methods         *
    #**************************************************************************************
    /**
     * Open product on FrontEnd by product name
     *
     * @param string $productName
     * @param bool $checkPage
     * @see https://jira.corp.x.com/browse/MAUTOSEL-536
     */
    public function frontOpenProduct($productName, $checkPage = true)
    {
        if (!is_string($productName)) {
            $this->fail('Wrong data to open a product');
        }
        $productUrl = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '-', $productName)), '-');
        $this->addParameter('productUrl', $productUrl);
        $this->addParameter('elementTitle', $productName);
        $this->frontend('product_page', false);
        $this->assertEmptyPageErrors();
        $this->setCurrentPage($this->getCurrentLocationUimapPage()->getPageId());
        $this->addParameter('productName', $productName);
        if ($checkPage) {
            $openedProductName = $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT, 'product_name', 'text');
            $this->assertEquals($productName, $openedProductName,
                "Product with name '$openedProductName' is opened, but should be '$productName'");
        }
    }

    /**
     * Open product on FrontEnd by product Id
     *
     * @param string $productId
     * @param string $productName
     */
    public function frontOpenProductById($productId, $productName)
    {
        if (!is_string($productId)) {
            $this->fail('Wrong data to open a product');
        }
        $this->addParameter('id', $productId);
        $this->addParameter('elementTitle', $productName);
        $this->frontend('product_page_id', false);
        $this->assertEmptyPageErrors();
        $this->setCurrentPage($this->getCurrentLocationUimapPage()->getPageId());
        $this->addParameter('productName', $productName);
        $openedProductName = $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT, 'product_name', 'text');
        $this->assertEquals($productName, $openedProductName,
            "Product with name '$openedProductName' is opened, but should be '$productName'");
    }

    /**
     * Add product to shopping cart
     *
     * @param array|null $dataForBuy
     */
    public function frontAddProductToCart($dataForBuy = null)
    {
        if ($dataForBuy) {
            $this->frontFillBuyInfo($dataForBuy);
        }
        $openedProductName = $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT, 'product_name', 'text');
        $this->addParameter('productName', $openedProductName);
        $this->saveForm('add_to_cart');
        $this->assertMessageNotPresent('validation');
    }

    /**
     * Choose custom options and additional products
     *
     * @param array $dataForBuy
     */
    public function frontFillBuyInfo($dataForBuy)
    {
        foreach ($dataForBuy as $value) {
            $fill = (isset($value['options_to_choose'])) ? $value['options_to_choose'] : array();
            $params = (isset($value['parameters'])) ? $value['parameters'] : array();
            foreach ($params as $k => $v) {
                $this->addParameter($k, $v);
            }
            $this->fillForm($fill);
        }
    }

    /**
     * Verify product info on frontend
     *
     * @param array $productData
     */
    public function verifyFrontendProductInfo(array $productData)
    {
        $this->frontOpenProduct($productData['general_name']);
        $actualProduct = $this->getFrontendProductData($productData);
        if (!empty($actualProduct['custom_options_data'])) {
            $this->verifyFrontendCustomOptions($actualProduct['custom_options_data'], $productData);
        }
        if (!empty($actualProduct['prices_tier_price_data'])) {
            foreach ($actualProduct['prices_tier_price_data'] as $tierPrice => $data) {
                foreach ($data as $fieldKey => $fieldValue) {
                    $expected = $productData['prices_tier_price_data'][$tierPrice][$fieldKey];
                    if ($fieldValue != $expected) {
                        $this->addVerificationMessage(
                            "Value for '$fieldKey' for '$tierPrice' tier price is not equal to specified.('"
                            . $fieldValue . "' != '" . $expected . "')");
                    }
                }
            }
        }
        foreach ($actualProduct as $fieldName => $fieldValue) {
            if (is_array($fieldValue)) {
                continue;
            }
            if ($productData[$fieldName] != $fieldValue) {
                $this->addVerificationMessage(
                    "Value for '$fieldName' field is not equal to specified.('" . $fieldValue . "' != '"
                    . $productData[$fieldName] . "')");
            }
            unset($actualProduct[$fieldName]);
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Get product info
     *
     * @param array $productData
     *
     * @return array
     */
    public function getFrontendProductData(array $productData)
    {
        $data = $this->getFrontendProductPrices();
        $data['prices_tier_price_data'] = $this->getFrontendProductTierPrices();
        $data['general_name'] = $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT, 'product_name', 'text');
        if (isset($productData['general_description'])) {
            $data['general_description'] = $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT, 'description',
                'text');
        }
        if (isset($productData['autosettings_short_description'])) {
            $data['autosettings_short_description'] = $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT,
                'short_description', 'text');
        }
        if ($this->controlIsPresent(self::FIELD_TYPE_INPUT, 'product_qty')) {
            $data['inventory_min_allowed_qty'] = $this->getControlAttribute(self::FIELD_TYPE_INPUT,
                'product_qty', 'selectedValue');
        }
        $availability = $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT, 'availability', 'text');
        $data['general_stock_availability'] = str_replace('stock', 'Stock', $availability);
        $data['custom_options_data'] = $this->getFrontendCustomOptionsInfo();

        return $data;
    }

    /**
     * Get product tier prices on product page
     * @return array
     */
    public function getFrontendProductTierPrices()
    {
        $tierPrices = $this->getControlElements(self::FIELD_TYPE_PAGEELEMENT, 'tier_price_line', null, false);
        $data = array();
        $index = 0;
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $tierPrice */
        foreach ($tierPrices as $tierPrice) {
            $price = trim($this->getChildElement($tierPrice, 'span[@class="price"]')->text());
            $price = floatval(preg_replace('/^[\D]+/', '', $price));
            $text = $tierPrice->text();
            list($qty) = explode($price, $text);
            $qty = preg_replace('/[^0-9]+/', '', $qty);
            $data['prices_tier_price_' . ++$index]['prices_tier_price_qty'] = $qty;
            $data['prices_tier_price_' . $index]['prices_tier_price_price'] = $price;
        }

        return $data;
    }

    /**
     * Get product prices on product page
     * @TODO Verified only for simple and virtual product
     * @return array
     */
    public function getFrontendProductPrices()
    {
        $priceData = $this->getControlAttribute(self::UIMAP_TYPE_FIELDSET, 'product_prices', 'text');
        if (!preg_match('/' . preg_quote("\n") . '/', $priceData)) {
            return array('general_price' => floatval(preg_replace('/^[\D]+/', '', $priceData)));
        }
        $priceData = explode("\n", $priceData);
        $additionalName = array();
        foreach ($priceData as $key => $price) {
            if (!preg_match('/(\d+\.\d+)|(\d+)$/', $price)) {
                $name = trim(str_replace(' ', '_', strtolower($price)), '_:');
                $additionalName[$key + 1] = $name;
                $additionalName[$key + 2] = $name;
                unset($priceData[$key]);
            }
        }
        $prices = array();
        foreach ($priceData as $key => $value) {
            list($name, $price) = explode(":", $value);
            $name = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '_', $name)), '_');
            if ($name == 'excl_tax' || $name == 'incl_tax') {
                if (isset($additionalName[$key])) {
                    $name = $additionalName[$key] . '_' . $name;
                } else {
                    $name = 'price_' . $name;
                }
            } elseif ($name == 'regular_price') {
                $name = 'price';
            }
            $prices[$name] = trim(preg_replace('/[^0-9\.]+/', '', $price));
        }
        foreach ($prices as $key => $value) {
            $prices[$key == 'price' ? 'general_' . $key : 'prices_' . $key] = floatval($value);
            unset($prices[$key]);
        }

        return $prices;
    }

    /**
     * Verify Custom Options Info on product page
     *
     * @param array $actualCustomOptions
     * @param array $product
     */
    public function verifyFrontendCustomOptions($actualCustomOptions, array $product)
    {
        $expectedOptions = $product['custom_options_data'];
        if (count($actualCustomOptions) != count($expectedOptions)) {
            $this->fail(
                "Amounts of the custom options are not equal to specified: ('" . count($expectedOptions) . "' != '"
                . count($actualCustomOptions) . "')");
        }
        foreach ($actualCustomOptions as $name => $data) {
            $expectedOptions[$name] = $this->_prepareFrontendCustomOptionsForVerify($expectedOptions[$name], $product);
            foreach ($expectedOptions[$name] as $fieldName => $fieldValue) {
                if (strpos($fieldName, 'custom_option_row_') !== false) {
                    $expectedOptions[$name][$fieldName] =
                        $this->_prepareFrontendCustomOptionsForVerify($expectedOptions[$name][$fieldName], $product);
                }
            }
        }
        $this->assertEquals($expectedOptions, $actualCustomOptions);
    }

    /**
     * Form custom options data for verify on product page
     *
     * @param array $expectedOption
     * @param array $productPrices
     *
     * @return array
     */
    private function _prepareFrontendCustomOptionsForVerify(array $expectedOption, array $productPrices)
    {
        $data = array();
        foreach ($expectedOption as $fieldName => $fieldValue) {
            switch ($fieldName) {
                case 'custom_options_sku':
                case 'custom_options_price_type':
                    break;
                case 'custom_options_price':
                    if (isset($expectedOption['custom_options_price_type'])
                        && $expectedOption['custom_options_price_type'] == 'Percent'
                    ) {
                        $basePrice = (isset($productPrices['prices_special_price']))
                            ? $productPrices['prices_special_price']
                            : $productPrices['general_price'];
                        $fieldValue = $fieldValue * 100 / $basePrice;
                    }
                    $data[$fieldName] = $fieldValue;
                    break;
                default:
                    $data[$fieldName] = $fieldValue;
                    break;
            }
        }

        return $data;
    }

    /**
     * Form Custom Options Info data array()
     *
     * @return array
     */
    public function getFrontendCustomOptionsInfo()
    {
        $fieldNames = array('title'                             => 'custom_options_general_title',
                            'price'                             => 'custom_options_price',
                            'type'                              => 'custom_options_general_input_type',
                            'required'                          => 'custom_options_general_is_required',
                            'sortOrder'                         => 'custom_options_general_sort_order',
                            'maximum_number_of_characters'      => 'custom_options_max_characters',
                            'allowed_file_extensions_to_upload' => 'custom_options_allowed_file_extension',
                            'maximum_image_width'               => 'custom_options_image_size_x',
                            'maximum_image_height'              => 'custom_options_image_size_y',
                            'option'                            => 'custom_option_row_');
        $fieldTypes = array('text'     => 'Field', 'file' => 'File', 'radio' => 'Radio Buttons',
                            'checkbox' => 'Checkbox', 'multiple' => 'Multiple Select', 'select' => 'Drop-down',
                            'dayTime'  => 'Date & Time', 'day' => 'Date', 'time' => 'Time', 'textarea' => 'Area');
        $typesWitOptions = array('Checkbox'  => 'checkbox', 'Multiple Select' => 'multiselect',
                                 'Radio Buttons' => 'radiobutton', 'Drop-down' => 'dropdown');
        $optionFieldset = "//div[@id='product-options-wrapper']//dt";
        $customOptionsInfo = array();
        $optionOrder = 0;
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $option */
        $options = $this->getElements($optionFieldset, false);
        foreach ($options as $option) {
            $optionTitleLine = $option->text();
            //Define 'Required' parameter
            $isRequired = (preg_match('/^\*/', $optionTitleLine)) ? 'Yes' : 'No';
            //Define 'Price' and 'Title' parameter
            list($optionTitle, $optionPrice) = $this->_parseCustomOptionTitleAndPrice($optionTitleLine);
            //Define option type
            $optionType = '';
            $elementBody = $this->getChildElement($option, '//following-sibling::dd[1]');
            $elementInput = $this->childElementIsPresent($elementBody, "//input[not(@type='hidden')]");
            $elementTextarea = $this->childElementIsPresent($elementBody, '//textarea');
            $elementSelect = $this->childElementIsPresent($elementBody, '//select');
            if ($elementInput) {
                $optionType = $fieldTypes[$elementInput->attribute('type')];
            }
            if ($elementTextarea) {
                $optionType = $fieldTypes[$elementTextarea->attribute('type')];
            }
            if ($elementSelect) {
                $selectElementCount = count($this->getChildElements($elementBody, '//select', false));
                if ($selectElementCount == 1) {
                    $optionType =
                        ($elementSelect->attribute('multiple')) ? $fieldTypes['multiple'] : $fieldTypes['select'];
                } elseif ($selectElementCount == 6) {
                    $optionType = $fieldTypes['dayTime'];
                } elseif (preg_match('/hour/', $elementSelect->attribute('name'))) {
                    $optionType = $fieldTypes['time'];
                } else {
                    $optionType = $fieldTypes['day'];
                }
            }
            //Form data array
            $optionId = 'option_' . ++$optionOrder;
            $customOptionsInfo[$optionId][$fieldNames['title']] = $optionTitle;
            $customOptionsInfo[$optionId][$fieldNames['type']] = $optionType;
            $customOptionsInfo[$optionId][$fieldNames['required']] = $isRequired;
            $customOptionsInfo[$optionId][$fieldNames['sortOrder']] = $optionOrder;
            $customOptionsInfo[$optionId][$fieldNames['price']] = $optionPrice;
            //Define additional info
            $elementsAdditionalText = $this->getChildElements($elementBody, '//p', false);
            /**@var  PHPUnit_Extensions_Selenium2TestCase_Element $value */
            foreach ($elementsAdditionalText as $value) {
                $text = trim($value->text());
                list($textKey, $textValue) = explode(':', $text);
                $textKey = trim(str_replace(' ', '_', strtolower($textKey)));
                $customOptionsInfo[$optionId][$fieldNames[$textKey]] = trim(preg_replace('/ px\.$/', '', $textValue));
            }
            //Define options for custom option with type 'multiselect'|'dropdown'|'radiobutton'|'checkbox'
            $valueOrder = 0;
            if (isset($typesWitOptions[$optionType])) {
                $type = $typesWitOptions[$optionType];
                $values = array();
                if ($type == self::FIELD_TYPE_MULTISELECT || $type == self::FIELD_TYPE_DROPDOWN) {
                    $values = $this->select($elementSelect)->selectOptionLabels();
                } elseif ($type == self::FIELD_TYPE_RADIOBUTTON || $type == self::FIELD_TYPE_CHECKBOX) {
                    $elementsValues = $this->getChildElements($elementBody, "//*[input[not(@type='hidden')]]");
                    foreach ($elementsValues as $value) {
                        $values[] = $value->text();
                    }
                }
                $values = array_diff($values, array('', '-- Please Select --'));
                foreach ($values as $value) {
                    list($optionValueTitle, $optionValuePrice) = $this->_parseCustomOptionTitleAndPrice($value);
                    $optionValueId = 'custom_option_row_' . ++$valueOrder;
                    $customOptionsInfo[$optionId][$optionValueId]['custom_options_title'] = $optionValueTitle;
                    $customOptionsInfo[$optionId][$optionValueId]['custom_options_price'] = $optionValuePrice;
                    $customOptionsInfo[$optionId][$optionValueId]['custom_options_sort_order'] = $valueOrder;
                }
            }
            $customOptionsInfo[$optionId] = array_diff($customOptionsInfo[$optionId], array(''));
        }

        return $customOptionsInfo;
    }

    /**
     * Parse custom option title and price
     *
     * @param string $textWithTitleAndPrice
     * @param bool $skipCurrency
     *
     * @return array
     */
    private function _parseCustomOptionTitleAndPrice($textWithTitleAndPrice, $skipCurrency = true)
    {
        $price = '';
        if (preg_match('/(\d+\.\d+)|(\d+)/', $textWithTitleAndPrice)) {
            $delimiter = (preg_match('/(-\D+)((\d+\.\d+)|(\d+))/', $textWithTitleAndPrice)) ? '-' : '+';
            list(, $price) = explode($delimiter, $textWithTitleAndPrice);
        }
        $title = trim(str_replace($price, '', $textWithTitleAndPrice), ' *+-');
        if ($skipCurrency) {
            $price = preg_replace('/^\D+/', '', $price);
        }
        if ($price !== '') {
            $price = floatval($price);
        }
        return array($title, $price);
    }

    #**************************************************************************************
    #*                                                    Backend Helper Methods          *
    #**************************************************************************************

    /**
     * Get product type by it's sku from Manage Products grid
     *
     * @param array $productSearch
     * @param string $columnName
     *
     * @return string
     */
    public function getProductDataFromGrid(array $productSearch, $columnName)
    {
        $productSearch = $this->_prepareDataForSearch($productSearch);
        $productLocator = $this->search($productSearch, 'product_grid');
        $this->assertNotNull($productLocator, 'Product is not found');
        $column = $this->getColumnIdByName($columnName);

        return trim($this->getElement($productLocator . '//td[' . $column . ']')->text());
    }

    /**
     * Define attribute set ID that used in product
     *
     * @param array $productSearchData
     *
     * @return string
     */
    public function defineAttributeSetUsedInProduct(array $productSearchData)
    {
        return $this->getProductDataFromGrid($productSearchData, 'Attribute Set');
    }

    /**
     * Check if product is present in products grid
     *
     * @param array $productSearchData
     *
     * @return bool
     */
    public function isProductPresentInGrid(array $productSearchData)
    {
        $this->_prepareDataForSearch($productSearchData);
        return $this->search($productSearchData, 'product_grid') !== null;
    }

    /**
     * Open product.
     *
     * @param array $searchData
     */
    public function openProduct(array $searchData)
    {
        //Search product
        $searchData = $this->_prepareDataForSearch($searchData);
        $productLocator = $this->search($searchData, 'product_grid');
        $this->assertNotNull($productLocator, 'Product is not found with data: ' . print_r($searchData, true));
        $productRowElement = $this->getElement($productLocator);
        $productUrl = $productRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Name');
        $cellElement = $this->getChildElement($productRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($productUrl));
        //Open product
        $this->url($productUrl);
        $this->validatePage('edit_product');
    }

    /**
     * Open tab on product page
     *
     * @param string $tabName
     */
    public function openProductTab($tabName)
    {
        if (!$this->controlIsVisible(self::UIMAP_TYPE_TAB, $tabName)) {
            $this->expandAdvancedSettings();
            $this->waitForControlVisible(self::UIMAP_TYPE_TAB, $tabName, 5);
        }
        $this->openTab($tabName);
    }

    /**
     * Expand Advanced Settings tab container
     */
    public function expandAdvancedSettings()
    {
        if (!$this->isControlExpanded(self::FIELD_TYPE_PAGEELEMENT, 'advanced_settings_header')) {
            $this->clickControl(self::FIELD_TYPE_PAGEELEMENT, 'advanced_settings_header', false);
            $this->waitForControlVisible(self::FIELD_TYPE_PAGEELEMENT, 'advanced_settings_panel', 5);
        }
    }

    /**
     * Select product type
     *
     * @param string $productType
     */
    public function selectTypeProduct($productType)
    {
        $this->clickButton('add_new_product_split_select', false);
        $this->addParameter('productType', $productType);
        $this->clickButton('add_product_by_type', false);
        $this->waitForPageToLoad();
        $this->addParameter('setId', $this->defineParameterFromUrl('set'));
        $this->validatePage();
    }

    /**
     * Create Product method using "Add Product" split button
     *
     * @param array $productData
     * @param string $productType
     * @param bool $isSave
     */
    public function createProduct(array $productData, $productType = 'simple', $isSave = true)
    {
        $this->selectTypeProduct($productType);
        if (isset($productData['product_attribute_set']) && $productData['product_attribute_set'] != 'Default') {
            $this->changeAttributeSet($productData['product_attribute_set']);
        }
        $this->fillProductInfo($productData);
        if ($isSave) {
            $this->saveProduct();
        }
    }

    /**
     * Save product using split button
     *
     * @param string $additionalAction continueEdit|new|duplicate|close
     * @param string $saveNewAttributeInSet
     */
    public function saveProduct($additionalAction = 'close', $saveNewAttributeInSet = 'current')
    {
        if ($additionalAction == 'continueEdit') {
            $this->moveto($this->getControlElement(self::FIELD_TYPE_PAGEELEMENT, 'admin_logo'));
            $this->saveAndContinueEdit('button', 'save_and_continue_edit');
            return;
        }
        $waitConditions = $this->getBasicXpathMessagesExcludeCurrent(array('success', 'error', 'validation'));
        $waitConditions[] = $this->_getControlXpath(self::UIMAP_TYPE_FIELDSET, 'choose_affected_attribute_set');
        $this->addParameter('additionalAction', $additionalAction);
        $this->moveto($this->getControlElement(self::FIELD_TYPE_PAGEELEMENT, 'admin_logo'));
        $this->waitForControlVisible('button', 'save_split_select')->click();
        $this->waitForControlVisible('button', 'save_product_by_action')->click();
        $this->waitForAjax();
        $this->waitForElementVisible($waitConditions);
        if ($this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'choose_affected_attribute_set')) {
            if (strtolower($saveNewAttributeInSet) == 'current') {
                $this->fillRadiobutton('current_attribute_set', 'Yes');
            } elseif ($saveNewAttributeInSet === null) {
                return;
            } else {
                $this->fillRadiobutton('new_attribute_set', 'Yes');
                $this->fillField('new_attribute_set_name', $saveNewAttributeInSet);
            }
            $this->clickButton('confirm', false);
            array_pop($waitConditions);
            $this->waitForAjax();
            $this->waitForElementVisible($waitConditions);
        }
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->addParameter('store', $this->defineParameterFromUrl('store'));
        $this->addParameter('tab', $this->defineParameterFromUrl('tab'));
        $this->validatePage();
    }

    /**
     * Form product data array for filling in/verifying
     *
     * @param array $productData
     * @param array $skipElements
     *
     * @return array
     */
    public function formProductData(array $productData, $skipElements = array('product_attribute_set'))
    {
        $data = array();
        foreach ($this->productTabs as $tabName) {
            foreach ($productData as $key => $value) {
                if (in_array($key, $skipElements)) {
                    unset($productData[$key]);
                    continue;
                }
                if (preg_match('/^' . $tabName . '/', $key)) {
                    $data[$tabName][$key] = $value;
                    unset($productData[$key]);
                }
            }
        }
        if (!empty($productData)) {
            $this->fail('There are data that will not be filled not on one tab:' . print_r($productData, true));
        }

        return $data;
    }

    /**
     * Fill Product info
     *
     * @param array $productData
     */
    public function fillProductInfo(array $productData)
    {
        if (isset($productData['product_online_status'])) {
            $this->selectOnlineStatus($productData['product_online_status']);
            unset($productData['product_online_status']);
        }
        $data = $this->formProductData($productData);
        foreach ($data as $tabName => $tabData) {
            $this->fillProductTab($tabData, $tabName);
        }
    }

    /**
     * Verify Product Info
     *
     * @param array $productData
     * @param array $skipElements
     */
    public function verifyProductInfo(array $productData, $skipElements = array())
    {
        if (isset($productData['product_online_status']) && !in_array('product_online_status', $skipElements)) {
            $this->selectOnlineStatus($productData['product_online_status'], 'verify');
            unset($productData['product_online_status']);
        }
        if (isset($productData['product_attribute_set']) && !in_array('product_attribute_set', $skipElements)) {
            $actualSetName = $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT, 'attribute_set_name', 'text');
            if ($actualSetName != $productData['product_attribute_set']) {
                $this->addVerificationMessage('Product Attribute Set should be '
                    . $productData['product_attribute_set']);
            }
            unset($productData['product_attribute_set']);
        }
        $data = $this->formProductData($productData, $skipElements);
        foreach ($data as $tabName => $tabData) {
            $this->verifyProductTab($tabData, $tabName);
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Fill in Product Settings tab
     *
     * @param array $forAttributesTab
     * @param array $forInventoryTab
     * @param array $forWebsitesTab
     */
    public function updateThroughMassAction(array $forAttributesTab, array $forInventoryTab, array $forWebsitesTab)
    {
        $this->fillTab($forAttributesTab, 'attributes');
        $this->fillTab($forInventoryTab, 'inventory');
        $this->fillTab($forWebsitesTab, 'websites');
    }

    /**
     * Change attribute set
     *
     * @param string $newAttributeSet
     */
    public function changeAttributeSet($newAttributeSet)
    {
        $this->moveto($this->getControlElement(self::FIELD_TYPE_PAGEELEMENT, 'admin_logo'));
        $this->clickButton('attribute_set_toggle', false);
        $attributeSetField = $this->waitForControlVisible(self::FIELD_TYPE_INPUT, 'attribute_set_name');
        $attributeSetField->click();
        $this->waitForControlVisible(self::FIELD_TYPE_INPUT, 'attribute_set_name');
        $this->waitForControlVisible(self::FIELD_TYPE_PAGEELEMENT, 'attribute_set_list');
        $this->addParameter('attributeSet', $newAttributeSet);
        //Search attribute set among recent attribute sets or in full list if it's first time
        if ($this->_chooseItemInAttributeSelector('attribute_set')) {
            return;
        }
        //Click 'Show all..' link
        if (!$this->_chooseItemInAttributeSelector('show_all_attribute_sets')) {
            $this->fail("Attribute set $newAttributeSet cannot be selected. 'Show all..' link is absent");
        }
        //Search attribute set in full attribute set list
        if ($this->_chooseItemInAttributeSelector('attribute_set')) {
            return;
        }
        //Search attribute set using attribute search control
        $attributeSetField->value($newAttributeSet);
        $this->waitForControlVisible(self::FIELD_TYPE_INPUT, 'attribute_set_name');
        $this->waitForControlVisible(self::FIELD_TYPE_PAGEELEMENT, 'attribute_set_list');
        if (!$this->_chooseItemInAttributeSelector('attribute_set')) {
            $this->fail('Attribute set ' . $newAttributeSet . ' cannot be selected.');
        }
    }

    /**
     * Choose item in suggest control for changing attribute set
     *
     * @param string $controlName
     *
     * @return bool
     */
    protected function _chooseItemInAttributeSelector($controlName)
    {
        if ($this->controlIsVisible(self::FIELD_TYPE_LINK, $controlName)) {
            $attributeSet = $this->getControlElement(self::FIELD_TYPE_LINK, $controlName);
            $this->moveto($attributeSet);
            $attributeSet->click();
            $this->pleaseWait();
            return true;
        }
        return false;
    }

    /**
     * Get auto-incremented SKU
     *
     * @param string $productSku
     *
     * @return string
     */
    public function getGeneratedSku($productSku)
    {
        return $productSku . '-1';
    }

    /**
     * Fill user product attribute
     *
     * @param array $productData
     * @param string $tabName
     */
    public function fillUserAttributesOnTab(array $productData, $tabName)
    {
        $userFieldData = $tabName . '_user_attr';
        if (array_key_exists($userFieldData, $productData) && is_array($productData[$userFieldData])) {
            foreach ($productData[$userFieldData] as $fieldType => $dataArray) {
                if (!is_array($dataArray)) {
                    continue;
                }
                foreach ($dataArray as $fieldKey => $fieldValue) {
                    $this->addParameter('attributeCode' . ucfirst(strtolower($fieldType)), $fieldKey);
                    $fillFunction = 'fill' . ucfirst(strtolower($fieldType));
                    $this->$fillFunction($tabName . '_user_attr_' . $fieldType, $fieldValue);
                }
            }
        }
    }

    /**
     * Fill Product Tab
     *
     * @param array $tabData
     * @param string $tabName Value - general|prices|meta_information|images|
     * |design|autosettings|inventory|websites|related|up_sells
     * |cross_sells|custom_options|bundle_items|associated|downloadable_information
     *
     * @return bool
     */
    public function fillProductTab(array $tabData, $tabName = 'general')
    {
        switch ($tabName) {
            case 'general':
                $this->fillGeneralTab($tabData);
                break;
            case 'prices':
                $this->fillPricesTab($tabData);
                break;
            case 'websites':
                $this->fillWebsitesTab($tabData['websites']);
                break;
            case 'related':
            case 'up_sells':
            case 'cross_sells':
                $this->openProductTab($tabName);
                foreach (array_pop($tabData) as $value) {
                    $this->assignProduct($value, $tabName);
                }
                break;
            case 'custom_options':
                $this->fillCustomOptionsTab($tabData);
                break;
            case 'downloadable_information':
                $this->fillDownloadableInformationTab($tabData['downloadable_information_data']);
                break;
            default:
                $this->openProductTab($tabName);
                $this->fillTab($tabData, $tabName);
                $this->fillUserAttributesOnTab($tabData, $tabName);
                break;
        }
    }

    /**
     * Verify product info
     *
     * @param array $tabData
     * @param string $tabName
     */
    public function verifyProductTab(array $tabData, $tabName = 'general')
    {
        switch ($tabName) {
            case 'general':
                $this->verifyGeneralTab($tabData);
                break;
            case 'prices':
                $this->verifyPricesTab($tabData);
                break;
            case 'websites':
                $this->verifyWebsitesTab($tabData['websites']);
                break;
            case 'related':
            case 'up_sells':
            case 'cross_sells':
                $this->openProductTab($tabName);
                foreach (array_pop($tabData) as $value) {
                    $this->isAssignedProduct($value, $tabName);
                }
                break;
            case 'custom_options':
                $this->verifyCustomOptions($tabData['custom_options_data']);
                break;
            case 'downloadable_information':
                $this->verifyDownloadableInformationTab($tabData);
                break;
            default:
                $this->openProductTab($tabName);
                $this->verifyForm($tabData, $tabName);
                break;
        }
    }

    #*********************************************************************************
    #*                                               General Tab Helper Methods      *
    #*********************************************************************************
    /**
     * Fill data on General Tab
     *
     * @param array $generalTab
     */
    public function fillGeneralTab(array $generalTab)
    {
        $this->openProductTab('general');

        if (isset($generalTab['general_categories'])) {
            $categories = $generalTab['general_categories'];
            unset($generalTab['general_categories']);
        }
        if (isset($generalTab['general_configurable_attributes'])) {
            if (!$this->isControlExpanded(self::UIMAP_TYPE_FIELDSET, 'product_variations')) {
                $this->clickControl(self::FIELD_TYPE_LINK, 'is_configurable', false);
                $this->waitForControlVisible(self::FIELD_TYPE_INPUT, 'general_configurable_attribute_title');
            }
            $attributeTitle = $generalTab['general_configurable_attributes'];
            unset($generalTab['general_configurable_attributes']);
        }
        if (isset($generalTab['general_configurable_variations'])) {
            $configurableData = $generalTab['general_configurable_variations'];
            unset($generalTab['general_configurable_variations']);
        }
        if (isset($generalTab['general_bundle_items'])) {
            $bundleItems = $generalTab['general_bundle_items'];
            unset($generalTab['general_bundle_items']);
        }
        if (isset($generalTab['general_grouped_data'])) {
            $this->addProductsToGroup($generalTab['general_grouped_data']);
            unset($generalTab['general_grouped_data']);
        }
        $this->fillUserAttributesOnTab($generalTab, 'general');
        $this->fillTab($generalTab, 'general');
        if (isset($categories)) {
            $this->selectProductCategories($categories);
            $this->clearActiveFocus();
        }
        if (isset($attributeTitle)) {
            $this->fillConfigurableSettings($attributeTitle);
        }
        if (isset($configurableData)) {
            $this->assignConfigurableVariations($configurableData);
        }
        if (isset($bundleItems)) {
            $this->fillBundleItems($bundleItems);
        }
    }

    /**
     * Verify data on General Tab
     *
     * @param array $generalTab
     */
    public function verifyGeneralTab(array $generalTab)
    {
        $this->openProductTab('general');
        if (isset($generalTab['general_categories'])) {
            $this->isSelectedCategory($generalTab['general_categories']);
            unset($generalTab['general_categories']);
        }
        if (isset($generalTab['general_configurable_attributes'])) {
            $this->verifyConfigurableSettings($generalTab['general_configurable_attributes']);
            unset($generalTab['general_configurable_attributes']);
        }
        if (isset($generalTab['general_configurable_variations'])) {
            $this->verifyConfigurableVariations($generalTab['general_configurable_variations'], true);
            unset($generalTab['general_configurable_variations']);
        }
        if (isset($generalTab['general_bundle_items'])) {
            $this->verifyBundleItems($generalTab['general_bundle_items']);
            unset($generalTab['general_bundle_items']);
        }
        if (isset($generalTab['general_grouped_data'])) {
            $this->isAssignedProductToGroup($generalTab['general_grouped_data']);
            unset($generalTab['general_grouped_data']);
        }
        $this->verifyForm($generalTab, 'general');
    }

    public function selectOnlineStatus($statusData, $action = 'fill')
    {
        $script = "return jQuery('#product-online-switcher').prop('checked')";
        $status = $this->execute(array('script' => $script, 'args' => array()));
        if (($status && $statusData == 'Disabled') || (!$status && $statusData == 'Enabled')) {
            if ($action == 'fill') {
                $this->clickControl(self::FIELD_TYPE_PAGEELEMENT, 'product_online_status');
            } else {
                $this->addVerificationMessage('Product Online Status should be ' . $statusData);
            }
        }
    }

    /**
     * Select Product Categories on general tab
     *
     * @param string|array $categoryData
     */
    public function selectProductCategories($categoryData)
    {
        if (is_string($categoryData)) {
            $categoryData = explode(',', $categoryData);
            $categoryData = array_map('trim', $categoryData);
        }
        $selectedQty = $this->getControlCount(self::UIMAP_TYPE_FIELDSET, 'chosen_category');
        $this->clickControl(self::FIELD_TYPE_INPUT, 'general_categories', false);
        $this->waitUntil(function ($testCase) {
            /** @var Mage_Selenium_TestCase $testCase */
            $class = $testCase->getControlAttribute('field', 'general_categories', 'class');
            if (strpos($class, 'mage-suggest-state-loading') === false) {
                return true;
            }
        });
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'categories_list');
        $toSelect = '';
        foreach ($categoryData as $categoryPath) {
            foreach (explode('/', $categoryPath) as $categoryNode) {
                if (isset($trueCategory)) {
                    $trueSubCat = array();
                    foreach ($trueCategory as $data) {
                        $trueSubCat = array_merge($trueSubCat, $this->defineCorrectCategory($categoryNode, $data));
                    }
                    $trueCategory = $trueSubCat;
                } else {
                    $trueCategory = $this->defineCorrectCategory($categoryNode);
                }
                if (empty($trueCategory)) {
                    if (!preg_match('|' . preg_quote($categoryNode) . '$|', $categoryPath)) {
                        list($path) = explode($categoryNode, $categoryPath);
                        $this->fail("'$categoryNode' category with path = '$path' could not found.");
                    } else {
                        $this->createNewCategory($categoryPath);
                        $this->waitUntil(function ($testCase) use ($selectedQty) {
                            /** @var Mage_Selenium_TestCase $testCase */
                            if ($testCase->getControlCount('fieldset', 'chosen_category') == $selectedQty + 1) {
                                return true;
                            }
                        });
                        return;
                    }
                }
            }
            foreach ($trueCategory as $data) {
                $this->addParameter('categoryId', $data);
                $isSelected = $this->getControlAttribute('pageelement', 'category_node', 'class');
                if (strpos($isSelected, 'mage-suggest-selected') === false) {
                    $toSelect = $data;
                    break;
                }
            }
            unset($trueCategory);
            if (!$toSelect) {
                $this->fail("Category with path = '$categoryPath' could not be selected.");
            }
            $this->addParameter('categoryId', $toSelect);
            $this->getControlElement('link', 'category')->click();
            $this->waitUntil(function ($testCase) use ($selectedQty) {
                /** @var Mage_Selenium_TestCase $testCase */
                if ($testCase->getControlCount('fieldset', 'chosen_category') == $selectedQty + 1) {
                    return true;
                }
            });
            $selectedQty++;
        }
    }

    /**
     * Find category with valid name and parent
     *
     * @param string $categoryName
     * @param null|string $parentId
     *
     * @return array
     */
    public function defineCorrectCategory($categoryName, $parentId = null)
    {
        $isCorrectName = array();
        $this->addParameter('categoryName', $categoryName);
        $this->addParameter('categoryId', $parentId);
        $categoryType = ($parentId) ? 'sub_category' : 'root_category';
        if ($parentId && $this->controlIsPresent('link', 'expand_category')) {
            $this->clickControl('link', 'expand_category', false);
            $this->waitForControlVisible('link', 'any_sub_category');
        }
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $element*/
        foreach ($this->getControlElements('link', $categoryType, null, false) as $element) {
            $isCorrectName[] = $element->attribute('data-suggest-option');
        }
        return $isCorrectName;
    }

    /**
     * Create new category
     *
     * @param string $categoryPath
     * @param bool $nameIsSet
     */
    public function createNewCategory($categoryPath, $nameIsSet = false)
    {
        $explodeCategoryPath = explode('/', $categoryPath);
        $categoryName = array_pop($explodeCategoryPath);
        $parentPath = implode('/', $explodeCategoryPath);
        $parentName = end($explodeCategoryPath);
        //Open new_category form
        $this->clickButton('new_category', false);
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'new_category_form');
        //Fill or verify new category name field
        if (!$nameIsSet) {
            $this->fillField('name', $categoryName);
        } else {
            $actualName = $this->getControlAttribute(self::FIELD_TYPE_INPUT, 'name', 'selectedValue');
            $this->assertSame($categoryName, $actualName, 'Category Name is not moved from categories field');
        }
        //Fill and verify parent category field
        $this->getControlElement(self::FIELD_TYPE_INPUT, 'parent_category')->value($parentName);
        $this->waitForControlEditable(self::FIELD_TYPE_INPUT, 'parent_category');
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'parent_categories_list');
        $this->addParameter('categoryName', $parentName);
        $this->addParameter('categoryPath', str_replace('/', ' / ', $parentPath));
        if ($this->controlIsVisible(self::FIELD_TYPE_PAGEELEMENT, 'parent_no_category_found')
            || !$this->controlIsVisible(self::FIELD_TYPE_LINK, 'parent_found_category')
        ) {
            $this->fail('It is impossible to create category with path - ' . $parentPath);
        }
        $element = $this->getControlElement(self::FIELD_TYPE_LINK, 'parent_found_category');
        $this->moveto($element);
        $element->click();
        $this->waitForControlVisible(self::FIELD_TYPE_LINK, 'chosen_parent_category_delete');
        //Save
        $this->addParameter('categoryName', substr($categoryName, 0, 255));
        $waitConditions = array(
            $this->_getControlXpath(self::FIELD_TYPE_LINK, 'chosen_category_delete'),
            $this->_getControlXpath(self::UIMAP_TYPE_FIELDSET, 'new_category_form')
                . $this->_getMessageXpath('general_validation')
        );
        $this->clickButton('new_category_save', false);
        $this->waitForElementVisible($waitConditions);
    }

    /**
     * Verify that category is selected
     *
     * @param string $categoryPath
     */
    public function isSelectedCategory($categoryPath)
    {
        if (is_string($categoryPath)) {
            $categoryPath = explode(',', $categoryPath);
            $categoryPath = array_map('trim', $categoryPath);
        }
        $selectedNames = array();
        $expectedNames = array();
        $isSelected = $this->getControlElements(self::UIMAP_TYPE_FIELDSET, 'chosen_category', null, false);
        foreach ($isSelected as $el) {
            /** @var PHPUnit_Extensions_Selenium2TestCase_Element $el */
            $selectedNames[] = trim($el->text());
        }
        foreach ($categoryPath as $category) {
            $explodeCategory = explode('/', $category);
            $categoryName = end($explodeCategory);
            $expectedNames[] = $categoryName;
            if (!in_array($categoryName, $selectedNames)) {
                $this->addVerificationMessage("'$categoryName' category is not selected");
            }
        }
        if (count($selectedNames) != count($expectedNames)) {
            $this->addVerificationMessage("Added wrong qty of categories");
        }
    }

    /**
     * Select configurable attribute from searchable control for configurable product creation
     *
     * @param array $attributes
     */
    public function fillConfigurableSettings(array $attributes)
    {
        $allTitles = array();
        $attributeOrder = array();
        foreach ($attributes as $attributeData) {
            if (!isset($attributeData['general_configurable_attribute_title'])) {
                $this->fail('general_configurable_attribute_title is not set');
            }
            $allTitles[] = $title = $attributeData['general_configurable_attribute_title'];
            if (isset($attributeData['attribute_position'])) {
                $attributeOrder[$title] = $attributeData['attribute_position'];
                unset($attributeData['attribute_position']);
            }
            unset($attributeData['general_configurable_attribute_title']);
            $this->selectConfigurableAttribute($title);
            $this->selectConfigurableAttributeOptions($attributeData, $title);
            if (!isset($attributeData['use_all_options']) || strtolower($attributeData['use_all_options']) != 'yes') {
                $selected = $this->_getSelectedAttributeOptions($attributeData);
                $optionNames = $this->getConfigurableAttributeOptionsNames($title);
                $this->unselectConfigurableAttributeOptions(array_diff($optionNames, $selected), $title);
            }
        }
        $waitLocator = $this->_getControlXpath(self::UIMAP_TYPE_FIELDSET, 'variations_matrix_grid') . '//thead/tr[';
        $lastTittle = end($allTitles);
        foreach ($allTitles as $title) {
            $value = (strpos($title, "'")) ? "concat('" . str_replace('\'', "',\"'\",'", $title) . "')" : "'$title'";
            $waitLocator .= 'th=' . $value;
            if ($title != $lastTittle) {
                $waitLocator .= ' and ';
            }
        }
        $waitLocator .= ']';
        $this->orderBlocks($attributeOrder, 'attributeTitle', 'move_product_variation_attribute',
            'attribute_blocks_name');
        $this->clickButton('generate_product_variations', false);
        $this->pleaseWait();
        $this->waitForElementVisible($waitLocator);
    }

    /**
     * Get option names that was selected
     *
     * @param array $attributeData
     *
     * @return array
     */
    private function _getSelectedAttributeOptions(array $attributeData)
    {
        $return = array();
        foreach ($attributeData as $value) {
            if (!is_array($value)) {
                continue;
            }
            if (isset($value['associated_attribute_value'])
                && (isset($value['associated_attribute_value']) && $value['associated_attribute_value'] != 'No')
            ) {
                $return[] = $value['associated_attribute_value'];
            }
        }

        return $return;
    }

    /**
     * Verify configurable attribute
     *
     * @param string|array $attributes
     */
    public function verifyConfigurableSettings($attributes)
    {
        $attributePosition = array();
        foreach ($attributes as $attributeData) {
            if (!isset($attributeData['general_configurable_attribute_title'])) {
                $this->addVerificationMessage('general_configurable_attribute_title is not set');
                continue;
            }
            $title = $attributeData['general_configurable_attribute_title'];
            unset($attributeData['general_configurable_attribute_title']);
            if (isset($attributeData['attribute_position'])) {
                $attributePosition[$title] = $attributeData['attribute_position'];
                unset($attributeData['attribute_position']);
            } else {
                $attributePosition[$title] = 'noValue';
            }
            $this->addParameter('attributeTitle', $title);
            if (!$this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'product_variation_attribute')) {
                $this->addVerificationMessage('Attribute "' . $title . '" is not selected');
                continue;
            }
            foreach ($attributeData as $optionData) {
                if (isset($optionData['associated_attribute_value'])) {
                    $this->addParameter('attributeOption', $optionData['associated_attribute_value']);
                    unset($optionData['associated_attribute_value']);
                }
                $this->verifyForm($optionData, 'general');
            }
        }
        $this->verifyBlocksOrder($attributePosition, 'attribute_blocks_name');
    }

    /**
     * Select configurable attribute on Product page using searchable attribute selector control
     *
     * @param array $attributeTitle
     */
    public function selectConfigurableAttribute($attributeTitle)
    {
        $this->addParameter('attributeTitle', $attributeTitle);
        if ($this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'product_variation_attribute')) {
            return;
        }
        $titleElement = $this->getControlElement(self::FIELD_TYPE_INPUT, 'general_configurable_attribute_title');
        $this->focusOnElement($titleElement);
        $titleElement->value($attributeTitle);
        $this->waitForControlVisible(self::FIELD_TYPE_PAGEELEMENT, 'configurable_attributes_list');
        $this->waitForControlVisible(self::FIELD_TYPE_LINK, 'configurable_attribute_select', 10);
        $this->getControlElement(self::FIELD_TYPE_LINK, 'configurable_attribute_select')->click();
        $this->waitForControlEditable(self::UIMAP_TYPE_FIELDSET, 'product_variation_attribute');
    }

    /**
     * Select configurable attribute options
     *
     * @param array $optionsData
     * @param string $attributeTitle
     */
    public function selectConfigurableAttributeOptions(array $optionsData, $attributeTitle)
    {
        $this->addParameter('attributeTitle', $attributeTitle);
        $optionNames = $this->getConfigurableAttributeOptionsNames($attributeTitle);
        if (isset($optionsData['use_all_options']) && strtolower($optionsData['use_all_options']) == 'yes') {
            foreach ($optionNames as $name) {
                $this->addParameter('attributeOption', $name);
                $this->fillCheckbox('include_variation_attribute', 'Yes');
            }
            unset($optionsData['use_all_options']);
        }
        foreach ($optionsData as $optionData) {
            if (isset($optionData['associated_attribute_value'])) {
                $this->addParameter('attributeOption', $optionData['associated_attribute_value']);
                unset($optionData['associated_attribute_value']);
            }
            if (!empty($optionData)) {
                $this->fillFieldset($optionData, 'product_variation_attribute');
            }
        }
    }

    /**
     * Unselect Configurable Attributes
     *
     * @param array|string $unselectOptions
     * @param string $attributeName
     */
    public function unselectConfigurableAttributeOptions($unselectOptions, $attributeName)
    {
        $this->addParameter('attributeTitle', $attributeName);
        if (is_string($unselectOptions)) {
            $unselectOptions = explode(',', $unselectOptions);
            $unselectOptions = array_map('trim', $unselectOptions);
        }
        foreach ($unselectOptions as $name) {
            $this->addParameter('attributeOption', $name);
            $this->fillCheckbox('include_variation_attribute', 'No');
        }
    }

    /**
     * Get configurable attribute options names
     *
     * @param $attributeName
     *
     * @return array
     */
    public function getConfigurableAttributeOptionsNames($attributeName)
    {
        $names = array();
        $this->addParameter('attributeTitle', $attributeName);
        $options = $this->getControlElements(self::FIELD_TYPE_PAGEELEMENT, 'option_line');
        foreach ($options as $option) {
            $names[] = $this->getChildElement($option, 'td')->text();
        }

        return array_diff($names, array(''));
    }

    /**
     * Assign Configurable Variations
     *
     * @param array $assignData
     */
    public function assignConfigurableVariations(array $assignData)
    {
        if (!$this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'variations_matrix_grid')) {
            $this->fail('Product variations grid is not present on the page');
        }
        $variationTable = $this->_getControlXpath(self::UIMAP_TYPE_FIELDSET, 'variations_matrix_grid');
        $headRowNames = $this->getTableHeadRowNames($variationTable);
        foreach ($assignData as $assignProduct) {
            if (!isset($assignProduct['associated_attributes'])) {
                $this->fail('Associated attributes data is required for product selection');
            }
            $attributes = $assignProduct['associated_attributes'];
            unset($assignProduct['associated_attributes']);
            //Define and add param that clarifies what a line in table will be use
            $param = $this->formAssignConfigurableParam($attributes, $headRowNames);
            $this->addParameter('attributeSearch', $param);
            $this->fillCheckbox('include_variation', 'Yes');
            $trLocator = $this->formSearchXpath($assignProduct, "//tbody/tr");
            //If product is selected
            if ($this->elementIsPresent($variationTable . $trLocator . "[$param]")) {
                return;
            }
            //If product is not selected
            $productTable = $this->_getControlXpath(self::UIMAP_TYPE_FIELDSET, 'select_associated_product');
            $this->clickButton('choose', false);
            $this->pleaseWait();
            $this->waitForElementVisible($productTable);
            //Define if product created.
            $selectParam = $this->formAssignConfigurableParam($attributes, $this->getTableHeadRowNames($productTable));
            $select = false;
            for (; ;) {
                if ($this->elementIsPresent($productTable . $trLocator . "[$selectParam]")) {
                    $select = true;
                    break;
                } elseif ($this->controlIsVisible(self::FIELD_TYPE_LINK, 'next_page')) {
                    $pageIndex = $this->getControlAttribute(self::FIELD_TYPE_LINK, 'product_page', 'value') + 1;
                    $this->addParameter('pageIndex', $pageIndex);
                    $this->clickControl(self::FIELD_TYPE_LINK, 'next_page', false);
                    $this->waitForControlEditable(self::FIELD_TYPE_PAGEELEMENT, 'opened_product_page');
                } else {
                    break;
                }
            }
            if ($select) {
                //Select created product
                $this->getElement($productTable . $trLocator . "[$selectParam]")->click();
                $this->waitForElementVisible($variationTable . $trLocator . "[$param]");
            } else {
                //Fill in data for new product
                $this->clickControl(self::FIELD_TYPE_LINK, 'close_select_associated_product', false);
                if (!$this->controlIsPresent(self::FIELD_TYPE_INPUT, 'associated_name')) {
                    $this->fail('Product is not exist and you can not create it(already another product is selected)');
                }
                $this->fillFieldset($assignProduct, 'variations_matrix_grid');
            }
        }
    }

    /**
     * Define parameter that clarifies what a line in table will be use
     *
     * @param array $attributesData
     * @param array $tableHeadNames
     *
     * @return string
     */
    public function formAssignConfigurableParam(array $attributesData, array $tableHeadNames)
    {
        $data = array();
        foreach ($attributesData as $assignAttribute) {
            $lineParameter = 'td';
            if (isset($assignAttribute['associated_attribute_name'])) {
                $rowId = array_search($assignAttribute['associated_attribute_name'], $tableHeadNames);
                $lineParameter .= '[' . ($rowId + 1) . ']';

            }
            if (isset($assignAttribute['associated_attribute_value'])) {
                $lineParameter .= "[normalize-space(text())='" . $assignAttribute['associated_attribute_value'] . "']";
            }
            $data[] = $lineParameter;
        }

        return implode(' and ', $data);
    }

    /**
     * Unassign all associated products in configurable product
     */
    public function unassignAllConfigurableVariations()
    {
        if (!$this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'variations_matrix_grid')) {
            return;
        }
        $variationsCount = $this->getControlCount(self::FIELD_TYPE_PAGEELEMENT, 'variation_line');
        while ($variationsCount > 0) {
            $this->addParameter('attributeSearch', $variationsCount--);
            $this->fillCheckbox('include_variation', 'No');
        }
    }

    /**
     * Assign all associated products in configurable product
     */
    public function assignAllConfigurableVariations()
    {
        if (!$this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'variations_matrix_grid')) {
            return;
        }
        $variationsCount = $this->getControlCount(self::FIELD_TYPE_PAGEELEMENT, 'variation_line');
        while ($variationsCount > 0) {
            $this->addParameter('attributeSearch', $variationsCount--);
            $this->fillCheckbox('include_variation', 'Yes');
        }
    }

    /**
     * Check variation matrix combinations
     *
     * @param array $matrixData
     * @param bool $isAssignedData
     *
     * @return bool
     */
    public function verifyConfigurableVariations(array $matrixData, $isAssignedData = false)
    {
        $actualData = $this->getConfigurableVariationsData();
        if (count($actualData) != count($matrixData)) {
            $this->addVerificationMessage('Not all variations are represented in variation matrix');

            return false;
        }
        foreach ($matrixData as $line => $lineData) {
            $actualLine = $actualData[$line];
            $lineData['associated_display'] = ($isAssignedData) ? 'Yes' : 'No';
            foreach ($lineData as $fieldName => $fieldValue) {
                if ($fieldName != 'associated_attributes' && $actualLine[$fieldName] != $fieldValue) {
                    $this->addVerificationMessage(
                        $fieldName . ' value for ' . $line . " variation is not equal to specified('" . $fieldValue
                        . "' != '" . $actualLine[$fieldName] . "')");
                    continue;
                }
                if ($fieldName == 'associated_attributes') {
                    foreach ($fieldValue as $attribute => $attributeData) {
                        foreach ($attributeData as $key => $value) {
                            if ($actualLine['associated_attributes'][$attribute][$key] == $value) {
                                continue;
                            }
                            $this->addVerificationMessage(
                                $key . ' value for associated ' . $attribute . " attribute is not equal to specified('"
                                . $value . "' != '" . $actualLine['associated_attributes'][$attribute][$key] . "')");
                        }
                    }
                }
            }
        }

        return ($this->getParsedMessages('verification') == null);
    }

    /**
     * Get Configurable Variations Data in table
     * @return array
     */
    public function getConfigurableVariationsData()
    {
        $generalFields = array('Display', 'Image', 'Name', 'Price', 'SKU', 'Quantity', 'Weight');
        $data = array();
        $option = 0;
        $lineElements = $this->getControlElements(self::FIELD_TYPE_PAGEELEMENT, 'variation_line');
        $cellNames = $this->getTableHeadRowNames($this->_getControlXpath(self::UIMAP_TYPE_FIELDSET,
            'variations_matrix_grid'));
        /**@var  PHPUnit_Extensions_Selenium2TestCase_Element $tdElement */
        foreach ($lineElements as $rowElement) {
            $lineData = array();
            $cellElements = $this->getChildElements($rowElement, 'td');
            foreach ($cellElements as $key => $tdElement) {
                if (!isset($cellNames[$key])) {
                    continue;
                }
                $cellName = trim($cellNames[$key], ' *');
                $inputElement = $this->childElementIsPresent($tdElement, 'input[not(@type="hidden")]');
                if (!$inputElement) {
                    $lineData[$cellName] = trim(str_replace('Choose', '', $tdElement->text()));
                } elseif ($cellName == 'Display') {
                    $lineData[$cellName] = ($inputElement->selected()) ? 'Yes' : 'No';
                } else {
                    $lineData[$cellName] = $inputElement->value();
                }
            }
            $form = array();
            $attribute = 0;
            foreach ($lineData as $cell => $cellData) {
                if (in_array($cell, $generalFields)) {
                    $fieldName = 'associated_' . trim(strtolower(str_replace(' ', '_', $cell)), '_');
                    $form[$fieldName] = $cellData;
                } else {
                    $name = 'attribute_' . ++$attribute;
                    $form['associated_attributes'][$name]['associated_attribute_name'] = $cell;
                    $form['associated_attributes'][$name]['associated_attribute_value'] = $cellData;
                }
            }
            $data['configurable_' . ++$option] = $form;
        }

        return $data;
    }

    /**
     * Exclude/include attribute's value from process of generation matrix
     *
     * @param string $attributeTitle
     * @param string $optionName
     * @param bool $select
     */
    public function changeAttributeValueSelection($attributeTitle, $optionName, $select = true)
    {
        $this->addParameter('attributeTitle', $attributeTitle);
        $this->addParameter('attributeOption', $optionName);
        $this->fillCheckbox('include_variation_attribute', ($select ? 'Yes' : 'No'));
    }

    #*********************************************************************************
    #*                                               Prices Tab Helper Methods       *
    #*********************************************************************************
    /**
     * Fill data on Prices Tab
     *
     * @param array $pricesTab
     */
    public function fillPricesTab(array $pricesTab)
    {
        $this->openProductTab('prices');
        if (isset($pricesTab['prices_tier_price_data'])) {
            foreach ($pricesTab['prices_tier_price_data'] as $value) {
                $this->addTierPrice($value);
            }
            unset($pricesTab['prices_tier_price_data']);
        }
        if (isset($pricesTab['prices_group_price_data'])) {
            foreach ($pricesTab['prices_group_price_data'] as $value) {
                $this->addGroupPrice($value);
            }
            unset($pricesTab['prices_group_price_data']);
        }
        if (!empty($pricesTab)) {
            $this->fillTab($pricesTab, 'prices');
        }
        $this->fillUserAttributesOnTab($pricesTab, 'prices');
    }

    /**
     * Verify data on Prices Tab
     *
     * @param array $pricesTab
     */
    public function verifyPricesTab($pricesTab)
    {
        $this->openProductTab('prices');
        if (isset($pricesTab['prices_tier_price_data'])) {
            $this->verifyTierPrices($pricesTab['prices_tier_price_data']);
            unset($pricesTab['prices_tier_price_data']);
        }
        if (isset($pricesTab['prices_group_price_data'])) {
            $this->verifyGroupPrices($pricesTab['prices_group_price_data']);
            unset($pricesTab['prices_group_price_data']);
        }
        $this->verifyForm($pricesTab, 'prices');
    }

    /**
     * Add Tier Price
     *
     * @param array $tierPriceData
     */
    public function addTierPrice(array $tierPriceData)
    {
        $rowNumber = $this->getControlCount(self::UIMAP_TYPE_FIELDSET, 'tier_price_row');
        $this->addParameter('tierPriceId', $rowNumber);
        $this->clickButton('add_tier_price', false);
        if (isset($tierPriceData['prices_tier_price_website'])
            && !$this->controlIsVisible(self::FIELD_TYPE_DROPDOWN, 'prices_tier_price_website')
        ) {
            unset($tierPriceData['prices_tier_price_website']);
        }
        $this->fillForm($tierPriceData, 'prices');
    }

    /**
     * Verify Tier Prices
     *
     * @param array $tierPriceData
     *
     * @return boolean
     */
    public function verifyTierPrices(array $tierPriceData)
    {
        $rowQty = $this->getControlCount(self::UIMAP_TYPE_FIELDSET, 'tier_price_row');
        $needCount = count($tierPriceData);
        if ($needCount != $rowQty) {
            $this->addVerificationMessage(
                'Product must be contains ' . $needCount . 'Tier Price(s), but contains ' . $rowQty);

            return false;
        }
        $identifier = 0;
        foreach ($tierPriceData as $value) {
            $this->addParameter('tierPriceId', $identifier);
            if (isset($value['prices_tier_price_website'])
                && !$this->controlIsVisible(self::FIELD_TYPE_DROPDOWN, 'prices_tier_price_website')
            ) {
                unset($value['prices_tier_price_website']);
            }
            $this->verifyForm($value, 'prices');
            $identifier++;
        }

        return true;
    }

    /**
     * Add Group Price
     *
     * @param array $groupPriceData
     */
    public function addGroupPrice(array $groupPriceData)
    {
        $rowNumber = $this->getControlCount(self::UIMAP_TYPE_FIELDSET, 'group_price_row');
        $this->addParameter('groupPriceId', $rowNumber);
        $this->clickButton('add_group_price', false);
        if (isset($groupPriceData['prices_group_price_website'])
            && !$this->controlIsVisible(self::FIELD_TYPE_DROPDOWN, 'prices_group_price_website')
        ) {
            unset($groupPriceData['prices_group_price_website']);
        }
        $this->fillForm($groupPriceData, 'prices');
    }

    /**
     * Verify Group Price
     *
     * @param array $groupPriceData
     *
     * @return bool
     */
    public function verifyGroupPrices(array $groupPriceData)
    {
        $rowQty = $this->getControlCount(self::UIMAP_TYPE_FIELDSET, 'group_price_row');
        $needCount = count($groupPriceData);
        if ($needCount != $rowQty) {
            $this->addVerificationMessage(
                'Product must be contains ' . $needCount . 'Group Price(s), but contains ' . $rowQty);

            return false;
        }
        $identifier = 0;
        foreach ($groupPriceData as $value) {
            $this->addParameter('groupPriceId', $identifier);
            if (isset($groupPriceData['prices_group_price_website'])
                && !$this->controlIsVisible(self::FIELD_TYPE_DROPDOWN, 'prices_group_price_website')
            ) {
                unset($groupPriceData['prices_group_price_website']);
            }
            $this->verifyForm($value, 'prices');
            $identifier++;
        }

        return true;
    }

    #*********************************************************************************
    #*                          Websites Tab Helper Methods                          *
    #*********************************************************************************
    /**
     * Fill data on Websites Tab
     *
     * @param array|string $websiteData
     */
    public function fillWebsitesTab($websiteData)
    {
        if (!$this->controlIsPresent(self::UIMAP_TYPE_TAB, 'websites') && $websiteData == 'Main Website') {
            return;
        }
        $this->openProductTab('websites');
        $websites = explode(',', $websiteData);
        $websites = array_map('trim', $websites);
        foreach ($websites as $website) {
            $this->addParameter('websiteName', $website);
            $this->assertTrue($this->controlIsPresent(self::FIELD_TYPE_CHECKBOX, 'websites'),
                'Website with name "' . $website . '" does not exist');
            $this->fillCheckbox('websites', 'Yes');
        }
    }

    /**
     * Verify data on Websites Tab
     *
     * @param array|string $websiteData
     */
    public function verifyWebsitesTab($websiteData)
    {
        if (!$this->controlIsPresent(self::UIMAP_TYPE_TAB, 'websites') && $websiteData == 'Main Website') {
            return;
        }
        $this->openProductTab('websites');
        $websites = explode(',', $websiteData);
        $websites = array_map('trim', $websites);
        foreach ($websites as $website) {
            $this->addParameter('websiteName', $website);
            $this->assertTrue($this->controlIsPresent(self::FIELD_TYPE_CHECKBOX, 'websites'),
                'Website with name "' . $website . '" does not exist');
            if (!$this->getControlAttribute(self::FIELD_TYPE_CHECKBOX, 'websites', 'selectedValue')) {
                $this->addVerificationMessage('Website with name "' . $website . '" is not selected');
            }
        }
    }

    #*********************************************************************************
    #*        Related Products', 'Up-sells' or 'Cross-sells' Tab Helper Methods      *
    #*********************************************************************************
    /**
     * Assign product. Use for fill in 'Related Products', 'Up-sells' or 'Cross-sells' tabs
     *
     * @param array $data
     * @param string $tabName
     */
    public function assignProduct(array $data, $tabName)
    {
        $fillingData = array();
        foreach ($data as $key => $value) {
            if (!preg_match('/^' . $tabName . '_search_/', $key)) {
                $fillingData[$key] = $value;
                unset($data[$key]);
            }
        }
        $this->searchAndChoose($data, $tabName);
        //Fill in additional data
        if ($fillingData) {
            $xpathTR = $this->formSearchXpath($data);
            $this->addParameter('productXpath', $xpathTR);
            $this->fillForm($fillingData, $tabName);
        }
    }

    /**
     * Verify that product is assigned
     *
     * @param array $data
     * @param string $fieldSetName
     */
    public function isAssignedProduct(array $data, $fieldSetName)
    {
        $fillingData['select_' . $fieldSetName . '_product'] = 'Yes';
        foreach ($data as $key => $value) {
            if (!preg_match('/^' . $fieldSetName . '_search_/', $key)) {
                $fillingData[$key] = $value;
                unset($data[$key]);
            }
        }
        $productLocator = $this->formSearchXpath($data);
        if (!$this->elementIsPresent($productLocator)) {
            $this->addVerificationMessage($fieldSetName . " tab: Product is not assigned with data: \n"
                . print_r($data, true));
            return;
        }
        $this->addParameter('productXpath', $productLocator);
        $this->verifyForm($fillingData, $fieldSetName);
    }

    /**
     * Unselect any associated product(as up_sells, cross_sells, related) to opened product
     *
     * @param string $type
     * @param bool $saveChanges
     */
    public function unselectAssociatedProduct($type, $saveChanges = false)
    {
        $this->openProductTab($type);
        $this->addParameter('tableXpath', $this->_getControlXpath(self::UIMAP_TYPE_FIELDSET, $type));
        if ($this->controlIsPresent(self::UIMAP_TYPE_MESSAGE, 'specific_table_no_records_found')) {
            return;
        }
        $this->moveto($this->getControlElement(self::FIELD_TYPE_PAGEELEMENT, 'admin_logo'));
        $this->fillCheckbox($type . '_select_all', 'No');
        if (!$saveChanges) {
            return;
        }
        $this->saveProduct('continueEdit');
        $this->assertTrue($this->controlIsPresent(self::UIMAP_TYPE_MESSAGE, 'specific_table_no_records_found'),
            'There are products assigned to "' . $type . '" tab');
        $this->refresh();
        $this->waitForControlVisible('checkbox', $type . '_select_all');
    }

    #*********************************************************************************
    #*                      Custom Options' Tab Helper Methods                       *
    #*********************************************************************************
    /**
     * Fill Custom Options
     *
     * @param array $productData
     *
     */
    public function fillCustomOptionsTab(array $productData)
    {
        $this->openProductTab('custom_options');
        $orderedBlocks = array();
        foreach ($productData['custom_options_data'] as $value) {
            if (isset($value['custom_options_general_sort_order'])) {
                $orderedBlocks[$value['custom_options_general_title']]= $value['custom_options_general_sort_order'];
                unset($value['custom_options_general_sort_order']);
            }
            $this->addCustomOption($value);
        }
        $this->orderBlocks($orderedBlocks, 'optionName', 'move_option', 'custom_options_general_titles');
    }

    /**
     * Add Custom Option
     *
     * @param array $customOptionData
     */
    public function addCustomOption(array $customOptionData)
    {
        $optionId = $this->getControlCount(self::UIMAP_TYPE_FIELDSET, 'custom_option_set') + 1;
        $this->addParameter('optionId', $optionId);
        $this->clickButton('add_option', false);
        $this->fillForm($customOptionData, 'custom_options');
        $orderedRows = array();
        foreach ($customOptionData as $rowKey => $rowValue) {
            if (preg_match('/^custom_option_row/', $rowKey) && is_array($rowValue)) {
                $rowId = $this->getControlCount(self::FIELD_TYPE_PAGEELEMENT, 'custom_option_row') + 1;
                $this->addParameter('rowId', $rowId);
                $this->clickButton('add_row', false);
                $this->fillForm($rowValue, 'custom_options');
                if ((isset($rowValue['custom_options_sort_order'])) && (isset($rowValue['custom_options_title']))) {
                    $orderedRows[$rowValue['custom_options_title']]= $rowValue['custom_options_sort_order'];
                    unset($rowValue['custom_options_sort_order']);
                }
            }
        }
        $this->orderBlocks($orderedRows, 'rowId', 'move_custom_option_row', 'custom_options_titles');
    }

    /**
     * Verify Custom Options
     *
     * @param array $customOptionData
     *
     * @return boolean
     */
    public function verifyCustomOptions(array $customOptionData)
    {
        $this->openProductTab('custom_options');
        $optionsQty = $this->getControlCount(self::UIMAP_TYPE_FIELDSET, 'custom_option_set');
        $needCount = count($customOptionData);
        if ($needCount != $optionsQty) {
            $this->addVerificationMessage(
                'Product must be contains ' . $needCount . ' Custom Option(s), but contains ' . $optionsQty);

            return false;
        }
        $orderedBlocks = array();
        foreach ($customOptionData as $item) {
            if (isset($item['custom_options_general_sort_order']) && isset($item['custom_options_general_title'])) {
                $orderedBlocks[$item['custom_options_general_title']] = $item['custom_options_general_sort_order'];
            } else { //in order to form proper order if custom option doesn't have position
                $orderedBlocks[$item['custom_options_general_sort_order']] = 'noValue';
                unset($item['custom_options_general_sort_order']);
            }
        }
        if (!empty($orderedBlocks)) {
            $this->verifyBlocksOrder($orderedBlocks, 'custom_options_general_titles');
        }
        foreach($customOptionData as $customOption) {
            $optionId = $this->getCustomOptionIdByName($customOption['custom_options_general_title']);
            $this->addParameter('optionId', $optionId);
            $orderedRows = array();
            foreach ($customOption as $valueRow) {
                if (is_array($valueRow) && isset($valueRow['custom_options_sort_order'])) {
                    $orderedRows[$valueRow['custom_options_title']] = $valueRow['custom_options_sort_order'];
                }
            }
            if (!empty($orderedRows)) {
                $this->verifyBlocksOrder($orderedRows, 'custom_options_titles');
            }
            $this->verifyForm($customOption, 'custom_options');
        }
        return true;
    }

    /**
     * Get option id for selected row
     *
     * @param int $rowNum
     *
     * @return int|null
     */
    public function getCustomOptionIdByRow($rowNum)
    {
        $optionElements = $this->getControlElements(self::FIELD_TYPE_PAGEELEMENT, 'custom_option_verify_row');
        if (!isset($optionElements[$rowNum - 1])) {
            return null;
        }
        $optionId = $optionElements[$rowNum - 1]->attribute('id');
        foreach (explode('_', $optionId) as $value) {
            if (is_numeric($value)) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Get option id by custom option name
     *
     * @param string $optionName
     *
     * @return int|null
     */
    public function getCustomOptionIdByName($optionName)
    {
        $optionElements = $this->getControlElements(self::FIELD_TYPE_INPUT, 'custom_options_general_titles');
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $element*/
        foreach ($optionElements as $element) {
            if ($element->value() == $optionName) {
                $optionId = $element->attribute('id');
                foreach (explode('_', $optionId) as $value) {
                    if (is_numeric($value)) {
                        return $value;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Import custom options from existent product
     *
     * @param array $productData
     */
    public function importCustomOptions(array $productData)
    {
        $this->openProductTab('custom_options');
        $this->clickButton('import_options', false);
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'select_product_custom_option');
        foreach ($productData as $value) {
            $this->searchAndChoose($value, 'select_product_custom_option_grid');
        }
        $this->clickButton('import', false);
        $this->waitForControlNotVisible(self::UIMAP_TYPE_FIELDSET, 'select_product_custom_option');
    }

    /**
     * Delete all custom options
     */
    public function deleteAllCustomOptions()
    {
        $this->openProductTab('custom_options');
        while ($this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'custom_option_set')) {
            $this->assertTrue($this->buttonIsPresent('delete_custom_option'),
                $this->locationToString() . "Problem with 'Delete Option' button.\n"
                . 'Control is not present on the page');
            $this->clickButton('delete_custom_option', false);
        }
    }

    #*********************************************************************************
    #*                  Downloadable Option Tab Helper Methods                       *
    #*********************************************************************************
    /**
     * Fill data on Downloadable Information Tab
     *
     * @param array $downloadableData
     */
    public function fillDownloadableInformationTab(array $downloadableData)
    {
        $this->openProductTab('downloadable_information');
        $this->moveto($this->getControlElement(self::FIELD_TYPE_PAGEELEMENT, 'admin_logo'));
        if (!$this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'downloadable_sample')) {
            $this->getControlElement(self::FIELD_TYPE_LINK, 'downloadable_sample')->click();
        }
        if (!$this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'downloadable_link')) {
            $this->getControlElement(self::FIELD_TYPE_LINK, 'downloadable_link')->click();
        }
        foreach ($downloadableData as $key => $value) {
            if (preg_match('/^downloadable_sample_/', $key) && is_array($value)) {
                $this->addDownloadableOption($value, 'sample');
                unset($downloadableData[$key]);
            }
            if (preg_match('/^downloadable_link_/', $key) && is_array($value)) {
                $this->addDownloadableOption($value, 'link');
                unset($downloadableData[$key]);
            }
        }
        $this->fillTab($downloadableData, 'downloadable_information');
    }

    /**
     * Verify data on Downloadable Information Tab
     *
     * @param array $downloadableData
     */
    public function verifyDownloadableInformationTab(array $downloadableData)
    {
        $this->openProductTab('downloadable_information');
        $this->moveto($this->getControlElement(self::FIELD_TYPE_PAGEELEMENT, 'admin_logo'));
        if (!$this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'downloadable_sample')) {
            $this->getControlElement(self::FIELD_TYPE_LINK, 'downloadable_sample')->click();
        }
        if (!$this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'downloadable_link')) {
            $this->getControlElement(self::FIELD_TYPE_LINK, 'downloadable_link')->click();
        }
        foreach ($downloadableData as $key => $value) {
            if (preg_match('/^downloadable_sample_/', $key) && is_array($value)) {
                $this->verifyDownloadableOptions($value, 'sample');
                unset($downloadableData[$key]);
            }
            if (preg_match('/^downloadable_link_/', $key) && is_array($value)) {
                $this->verifyDownloadableOptions($value, 'link');
                unset($downloadableData[$key]);
            }
        }
        $this->verifyForm($downloadableData, 'downloadable_information');
    }

    /**
     * Add Sample for Downloadable product
     *
     * @param array $optionData
     * @param string $type
     */
    public function addDownloadableOption(array $optionData, $type)
    {
        $rowNumber = $this->getControlCount(self::FIELD_TYPE_PAGEELEMENT, 'added_downloadable_' . $type);
        $this->addParameter('rowId', $rowNumber);
        $this->clickButton('downloadable_' . $type . '_add_new_row', false);
        $this->fillForm($optionData, 'downloadable_information');
    }

    /**
     * Verify Downloadable Options
     *
     * @param array $optionsData
     * @param string $type
     *
     * @return bool
     */
    public function verifyDownloadableOptions(array $optionsData, $type)
    {
        $this->openProductTab('downloadable_information');
        $rowQty = $this->getControlCount(self::FIELD_TYPE_PAGEELEMENT, 'downloadable_' . $type . '_row');
        $needCount = count($optionsData);
        if ($needCount != $rowQty) {
            $this->addVerificationMessage(
                'Product must be contains ' . $needCount . ' Downloadable ' . $type . '(s), but contains ' . $rowQty);

            return false;
        }
        $identifier = 0;
        foreach ($optionsData as $value) {
            $this->addParameter('rowId', $identifier);
            $this->verifyForm($value, 'downloadable_information');
            $identifier++;
        }

        return ($this->getParsedMessages('verification') == null);
    }

    /**
     * Delete Samples/Links rows on Downloadable Information tab
     */
    public function deleteDownloadableInformation($type)
    {
        $this->openProductTab('downloadable_information');
        if (!$this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'downloadable_' . $type)) {
            $this->moveto($this->getControlElement(self::FIELD_TYPE_PAGEELEMENT, 'admin_logo'));
            $this->getControlElement(self::FIELD_TYPE_LINK, 'downloadable_' . $type)->click();
        }
        $rowQty = $this->getControlCount(self::FIELD_TYPE_PAGEELEMENT, 'added_downloadable_' . $type);
        if ($rowQty > 0) {
            while ($rowQty > 0) {
                $this->addParameter('rowId', $rowQty);
                $this->clickButton('delete_' . $type, false);
                $rowQty--;
            }
        }
    }

    #*********************************************************************************
    #*                         Bundle Items Tab Helper Methods                       *
    #*********************************************************************************

    /**
     * Fill Bundle Items data
     *
     * @param array $bundleItems
     */
    public function fillBundleItems(array $bundleItems)
    {
        $orderedBlocks = array();
        if (isset($bundleItems['ship_bundle_items'])) {
            $this->fillDropdown('ship_bundle_items', $bundleItems['ship_bundle_items']);
            unset($bundleItems['ship_bundle_items']);
        }
        foreach ($bundleItems as $item) {
            if (isset($item['bundle_items_position']) && isset($item['bundle_items_default_title'])) {
                $orderedBlocks[$item['bundle_items_default_title']] = $item['bundle_items_position'];
                unset($item['bundle_items_position']);
            }
            $this->addBundleOption($item);
        }
        $this->orderBlocks($orderedBlocks, 'itemTitle', 'move_product_bundle_item', 'bundle_items_names');
    }

    /**
     * Verify Bundle Items data
     *
     * @param array $bundleItems
     */
    public function verifyBundleItems(array $bundleItems)
    {
        if (isset($bundleItems['ship_bundle_items'])) {
            $selected = $this->getControlAttribute(self::FIELD_TYPE_DROPDOWN, 'ship_bundle_items', 'selectedLabel');
            if ($selected != $bundleItems['ship_bundle_items']) {
                $this->addVerificationMessage("ship_bundle_items: The stored value is not equal to specified: ('"
                                              . $bundleItems['ship_bundle_items'] . "' != '" . $selected . "')");
            }
            unset($bundleItems['ship_bundle_items']);
        }
        $this->verifyBundleOptions($bundleItems);
    }

    /**
     * Form Bundle Item data array for filling in/verifying
     *
     * @param array $itemData
     *
     * @return array
     */
    public function formBundleItemData(array $itemData)
    {
        $data = array('general' => array(), 'items' => array(), 'order' => array());
        foreach ($itemData as $key => $dataValue) {
            if (is_array($dataValue)) {
                foreach ($dataValue as $itemKey => $itemData) {
                    if ($itemKey == 'associated_search_name' || $itemKey == 'associated_search_sku') {
                        $data['items'][$key]['param'] = $itemData;
                        if (isset($dataValue['selection_item_position'])) {
                            $data['order'][$itemData] = $dataValue['selection_item_position'];
                        } else {
                            $data['order'][$itemData] = 'noValue';
                        }
                    }
                    if (preg_match('/^associated_search_/', $itemKey)) {
                        $data['items'][$key]['search'][$itemKey] = $itemData;
                    } elseif (preg_match('/^selection_item_/', $itemKey) && !preg_match('/_position$/', $itemKey)) {
                        $data['items'][$key]['fill'][$itemKey] = $itemData;
                    }
                }
            } else {
                $data['general'][$key] = $dataValue;
            }
        }

        return $data;
    }

    /**
     * Add Bundle Option
     *
     * @param array $bundleOptionData
     */
    public function addBundleOption(array $bundleOptionData)
    {
        $optionsCount = $this->getControlCount(self::FIELD_TYPE_PAGEELEMENT, 'bundle_item_row');
        $this->addParameter('optionId', $optionsCount);
        $this->clickButton('add_new_option', false);
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'new_bundle_option');
        $data = $this->formBundleItemData($bundleOptionData);
        if (!$this->isControlExpanded(self::UIMAP_TYPE_FIELDSET, 'new_bundle_option')) {
            $this->clickControl(self::FIELD_TYPE_PAGEELEMENT, 'is_collapsed', false);
        }
        $this->fillFieldset($data['general'], 'new_bundle_option');
        foreach ($data['items'] as $item) {
            if (!isset($item['search'])) {
                continue;
            }
            $optionName = isset($data['general']['bundle_items_default_title'])
                ? 'Option "' . $data['general']['bundle_items_default_title'] . '"'
                : 'New Option';
            $this->addParameter('optionName', $optionName);
            $this->clickButton('add_selection', false);
            $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'select_associated_product_option');
            $this->searchAndChoose($item['search'], 'select_associated_product_option');
            $this->clickButton('add_selected_products', false);
            if (isset($item['param'])) {
                $this->addParameter('productSku', $item['param']);
            }
            $this->waitForControlVisible(self::FIELD_TYPE_PAGEELEMENT, 'selected_option_product');
            if (isset($item['fill'])) {
                $this->fillFieldset($item['fill'], 'new_bundle_option');
            }
        }
        if (isset($data['order'])) {
            $this->orderBlocks($data['order'], 'productSku', 'move_bundle_product', 'bundle_assigned_products');
        }
    }

    /**
     * Verify Bundle Options
     *
     * @param array $bundleItemsData
     *
     * @return bool
     */
    public function verifyBundleOptions(array $bundleItemsData)
    {
        $optionsCount = $this->getControlCount(self::FIELD_TYPE_PAGEELEMENT, 'bundle_item_grid');
        $needCount = count($bundleItemsData);
        $optionId = 0;
        if ($needCount != $optionsCount) {
            $this->addVerificationMessage(
                'Product must be contains ' . $needCount . ' Bundle Item(s), but contains ' . $optionsCount);

            return false;
        }
        $bundleItemOrder = array();
        foreach ($bundleItemsData as $item) {
            if (isset($item['bundle_items_position']) && isset($item['bundle_items_default_title'])) {
                $bundleItemOrder[$item['bundle_items_default_title']] = $item['bundle_items_position'];
            } else { //in order to form proper order if bundle item doesn't have position
                $bundleItemOrder[$item['bundle_items_default_title']] = 'noValue';
            }
        }
        $this->verifyBlocksOrder($bundleItemOrder, 'bundle_items_names');
        $itemDataOrder = $this->getActualItemOrder(self::FIELD_TYPE_INPUT, 'bundle_items_names');
        foreach ($bundleItemsData as $option) {
            $optionData = $this->formBundleItemData($option);
            if (isset($option['bundle_items_default_title'])
                && isset($itemDataOrder[$option['bundle_items_default_title']])
            ) {
                $optionId = $itemDataOrder[$option['bundle_items_default_title']];
                $this->addParameter('optionId', $optionId - 1);
                if (!$this->isControlExpanded(self::UIMAP_TYPE_FIELDSET, 'new_bundle_option')) {
                    $this->clickControl(self::FIELD_TYPE_PAGEELEMENT, 'is_collapsed', false);
                }
            } else {
                $this->fail('Bundle item with name ' . $option['bundle_items_default_title'] . ' is absent');
            }
            $this->verifyForm($optionData['general'], 'general');
            foreach ($optionData['items'] as $item) {
                $productSku = (isset($item['param'])) ? $item['param'] : '';
                $this->addParameter('productSku', $productSku);
                if (!$this->controlIsPresent(self::FIELD_TYPE_PAGEELEMENT, 'bundle_item_grid_index_product')) {
                    $this->addVerificationMessage(
                        'Product with sku(name) ' . $productSku . ' is not assigned to bundle item ' . $optionId);
                } elseif (isset($item['fill'])) {
                    $this->verifyForm($item['fill'], 'general');
                }
            }
            if (isset($optionData['order'])) {
                $this->verifyBlocksOrder($optionData['order'], 'bundle_assigned_products');
            }
        }

        return ($this->getParsedMessages('verification') == null);
    }

    #*********************************************************************************
    #*                         Group Items Tab Helper Methods                       *
    #*********************************************************************************

    /**
     * Form Grouped data array for filling in/verifying
     *
     * @param array $productsData
     *
     * @return array
     */
    public function formGroupedData(array $productsData)
    {
        $data = array('items' => array(), 'order' => array(), 'qty' => array());
        foreach ($productsData as $product) {
            $param = $product['associated_search_sku'];
            if (isset($product['associated_product_default_qty'])) {
                $data['qty'][$param] = $product['associated_product_default_qty'];
                unset($product['associated_product_default_qty']);
            }
            if (isset($product['associated_product_position'])) {
                $data['order'][$param] = $product['associated_product_position'];
                unset($product['associated_product_position']);
            } else {
                $data['order'][$param] = 'noValue';
            }
            $data['items'][] = $product;
        }

        return $data;
    }

    /**
     * Add products to group product.
     *
     * @param array $data
     */
    public function addProductsToGroup(array $data)
    {
        $formedData = $this->formGroupedData($data);
        $countBefore = $this->getControlCount(self::FIELD_TYPE_PAGEELEMENT, 'grouped_assigned_products');
        $this->clickButton('add_products_to_group', false);
        $this->pleaseWait();
        $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'select_associated_product_option');
        foreach ($formedData['items'] as $product) {
            $this->searchAndChoose($product, 'select_associated_product_option');
        }
        $this->clickButton('add_selected_products', false);
        $this->waitForControlNotVisible(self::UIMAP_TYPE_FIELDSET, 'select_associated_product_option');
        $actualQty = $this->getControlCount(self::FIELD_TYPE_PAGEELEMENT, 'grouped_assigned_products');
        $this->assertEquals($countBefore + count($formedData['items']), $actualQty,
            'Products are not assigned to Grouped product');
        if (!empty($formedData['qty'])) {
            foreach ($formedData['qty'] as $productName => $qty) {
                $this->addParameter('productSku', $productName);
                $this->fillField('associated_product_default_qty', $qty);
            }
        }
        $this->orderBlocks($formedData['order'], 'productSku', 'move_grouped_product', 'grouped_assigned_products');
    }

    /**
     * Verify that product is assigned to grouped product
     *
     * @param array $data
     */
    public function isAssignedProductToGroup(array $data)
    {
        $formedData = $this->formGroupedData($data);
        foreach ($formedData['items'] as $product) {
            $this->addParameter('productSku', $product['associated_search_sku']);
            if ($this->controlIsVisible(self::FIELD_TYPE_PAGEELEMENT, 'selected_grouped_product')) {
                if (isset($formedData['qty'][$product['associated_search_sku']])
                    && $this->getControlAttribute(self::FIELD_TYPE_INPUT, 'associated_product_default_qty', 'value')
                       != $formedData['qty'][$product['associated_search_sku']]
                ) {
                    $this->addVerificationMessage("Invalid product qty with sku: \n"
                        . print_r($product['associated_search_sku'], true));
                }
            } else {
                $this->addVerificationMessage('general' . " tab: Product is not assigned with data: \n"
                    . print_r($product['associated_search_sku'], true));
            }
        }
        $this->verifyBlocksOrder($formedData['order'], 'grouped_assigned_products');
    }

    #*********************************************************************************
    #*                         Test  Methods for creating product                    *
    #*********************************************************************************
    /**
     * Create Configurable product
     *
     * @param bool $inSubCategory
     *
     * @return array
     */
    public function createConfigurableProduct($inSubCategory = false)
    {
        //Create category
        if ($inSubCategory) {
            $category = $this->loadDataSet('Category', 'sub_category_required');
            $catPath = $category['parent_category'] . '/' . $category['name'];
            $this->navigate('manage_categories', false);
            $this->categoryHelper()->checkCategoriesPage();
            $this->categoryHelper()->createCategory($category);
            $this->assertMessagePresent('success', 'success_saved_category');
            $returnCategory = array('name' => $category['name'], 'path' => $catPath);
        } else {
            $returnCategory = array('name' => 'Default Category', 'path' => 'Default Category');
        }
        //Create product
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $attrCode = $attrData['advanced_attribute_properties']['attribute_code'];
        $storeViewOptionsNames = array(
            $attrData['option_1']['store_view_titles']['Default Store View'],
            $attrData['option_2']['store_view_titles']['Default Store View'],
            $attrData['option_3']['store_view_titles']['Default Store View']
        );
        $adminOptionsNames = array(
            $attrData['option_1']['admin_option_name'],
            $attrData['option_2']['admin_option_name'],
            $attrData['option_3']['admin_option_name']
        );
        $download = $this->loadDataSet('SalesOrder', 'downloadable_product_for_order',
            array('downloadable_links_purchased_separately' => 'No', 'general_categories' => $returnCategory['path']));
        $download['general_user_attr'][self::FIELD_TYPE_DROPDOWN][$attrCode] = $adminOptionsNames[2];
        $configurable = $this->loadDataSet('SalesOrder', 'configurable_product_for_order',
            array('general_categories' => $returnCategory['path']),
            array(
                 'general_attribute_1' => $attrData['attribute_properties']['attribute_label'],
                 'associated_3'        => $download['general_sku'],
                 'var1_attr_value1'    => $adminOptionsNames[0],
                 'var1_attr_value2'    => $adminOptionsNames[1],
                 'var1_attr_value3'    => $adminOptionsNames[2]
            ));
        $associatedPr = $configurable['general_configurable_variations'];
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet(array('Product Details' => $attrCode));
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $this->navigate('manage_products');
        $this->createProduct($download, 'downloadable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'simple'             => array(
                'product_name' => $associatedPr['configurable_1']['associated_name'],
                'product_sku'  => $associatedPr['configurable_1']['associated_sku']
            ),
            'downloadable'       => array(
                'product_name' => $download['general_name'],
                'product_sku'  => $download['general_sku']
            ),
            'virtual'            => array(
                'product_name' => $associatedPr['configurable_2']['associated_name'],
                'product_sku'  => $associatedPr['configurable_2']['associated_sku']
            ),
            'configurable'       => array(
                'product_name' => $configurable['general_name'],
                'product_sku'  => $configurable['general_sku']
            ),
            'simpleOption'       => array(
                'option'       => $adminOptionsNames[0],
                'option_front' => $storeViewOptionsNames[0]
            ),
            'virtualOption'      => array(
                'option'       => $adminOptionsNames[1],
                'option_front' => $storeViewOptionsNames[1]
            ),
            'downloadableOption' => array(
                'option'       => $adminOptionsNames[2],
                'option_front' => $storeViewOptionsNames[2]
            ),
            'configurableOption' => array(
                'title' => $attrData['attribute_properties']['attribute_label'],
                'custom_option_dropdown' => $storeViewOptionsNames[0]
            ),
            'attribute'          => array(
                'title'       => $attrData['attribute_properties']['attribute_label'],
                'title_front' => $attrData['store_view_titles']['Default Store View'],
                'code'        => $attrCode
            ),
            'category'           => $returnCategory
        );
    }

    /**
     * Create Grouped product
     *
     * @param bool $inSubCategory
     *
     * @return array
     */
    public function createGroupedProduct($inSubCategory = false)
    {
        //Create category
        if ($inSubCategory) {
            $category = $this->loadDataSet('Category', 'sub_category_required');
            $catPath = $category['parent_category'] . '/' . $category['name'];
            $this->navigate('manage_categories', false);
            $this->categoryHelper()->checkCategoriesPage();
            $this->categoryHelper()->createCategory($category);
            $this->assertMessagePresent('success', 'success_saved_category');
            $returnCategory = array('name' => $category['name'], 'path' => $catPath);
        } else {
            $returnCategory = array('name' => 'Default Category', 'path' => 'Default Category');
        }
        //Create product
        $productCat = array('general_categories' => $returnCategory['path']);
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $productCat);
        $virtual = $this->loadDataSet('Product', 'virtual_product_visible', $productCat);
        $download = $this->loadDataSet('SalesOrder', 'downloadable_product_for_order',
            array('downloadable_links_purchased_separately' => 'No', 'general_categories' => $returnCategory['path']));
        $grouped = $this->loadDataSet('SalesOrder', 'grouped_product_for_order', $productCat,
            array(
                 'associated_1' => $simple['general_sku'],
                 'associated_2' => $virtual['general_sku'],
                 'associated_3' => $download['general_sku']
            ));
        $this->navigate('manage_products');
        $this->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($virtual, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($download, 'downloadable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($grouped, 'grouped');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'simple'        => array(
                'product_name' => $simple['general_name'],
                'product_sku'  => $simple['general_sku']
            ),
            'downloadable'  => array(
                'product_name' => $download['general_name'],
                'product_sku'  => $download['general_sku']
            ),
            'virtual'       => array(
                'product_name' => $virtual['general_name'],
                'product_sku'  => $virtual['general_sku']
            ),
            'grouped'       => array(
                'product_name' => $grouped['general_name'],
                'product_sku'  => $grouped['general_sku']
            ), 'category'   => $returnCategory,
            'groupedOption' => array(
                'subProduct_1' => $simple['general_name'],
                'subProduct_2' => $virtual['general_name'],
                'subProduct_3' => $download['general_name']
            )
        );
    }

    /**
     * Create Bundle product
     *
     * @param bool $inSubCategory
     *
     * @return array
     */
    public function createBundleProduct($inSubCategory = false)
    {
        //Create category
        if ($inSubCategory) {
            $category = $this->loadDataSet('Category', 'sub_category_required');
            $catPath = $category['parent_category'] . '/' . $category['name'];
            $this->navigate('manage_categories', false);
            $this->categoryHelper()->checkCategoriesPage();
            $this->categoryHelper()->createCategory($category);
            $this->assertMessagePresent('success', 'success_saved_category');
            $returnCategory = array('name' => $category['name'], 'path' => $catPath);
        } else {
            $returnCategory = array('name' => 'Default Category', 'path' => 'Default Category');
        }
        //Create product
        $productCat = array('general_categories' => $returnCategory['path']);
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $productCat);
        $virtual = $this->loadDataSet('Product', 'virtual_product_visible', $productCat);
        $bundle = $this->loadDataSet('SalesOrder', 'fixed_bundle_for_order', $productCat,
            array(
                 'add_product_1'   => $simple['general_sku'],
                 'price_product_1' => 0.99,
                 'add_product_2'   => $virtual['general_sku'],
                 'price_product_2' => 1.24

            ));
        $this->navigate('manage_products');
        $this->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($virtual, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($bundle, 'bundle');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'simple'       => array(
                'product_name' => $simple['general_name'],
                'product_sku'  => $simple['general_sku']
            ),
            'virtual'      => array(
                'product_name' => $virtual['general_name'],
                'product_sku'  => $virtual['general_sku']
            ),
            'bundle'       => array(
                'product_name' => $bundle['general_name'],
                'product_sku'  => $bundle['general_sku']
            ),
            'category'     => $returnCategory,
            'bundleOption' => array(
                'subProduct_1' => $simple['general_name'],
                'subProduct_2' => $virtual['general_name'],
                'subProduct_3' => $simple['general_name'],
                'subProduct_4' => $virtual['general_name']
            )
        );
    }

    /**
     * Create Downloadable product
     *
     * @param bool $inSubCategory
     *
     * @return array
     */
    public function createDownloadableProduct($inSubCategory = false)
    {
        //Create category
        if ($inSubCategory) {
            $category = $this->loadDataSet('Category', 'sub_category_required');
            $catPath = $category['parent_category'] . '/' . $category['name'];
            $this->navigate('manage_categories', false);
            $this->categoryHelper()->checkCategoriesPage();
            $this->categoryHelper()->createCategory($category);
            $this->assertMessagePresent('success', 'success_saved_category');
            $returnCategory = array('name' => $category['name'], 'path' => $catPath);
        } else {
            $returnCategory = array('name' => 'Default Category', 'path' => 'Default Category');
        }
        //Create product
        $assignCategory = array('general_categories' => $returnCategory['path']);
        $downloadable = $this->loadDataSet('Product', 'downloadable_product_visible', $assignCategory);
        $link = $downloadable['downloadable_information_data']['downloadable_link_1']['downloadable_link_row_title'];
        $linksTitle = $downloadable['downloadable_information_data']['downloadable_links_title'];
        $this->navigate('manage_products');
        $this->createProduct($downloadable, 'downloadable');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'downloadable'       => array(
                'product_name' => $downloadable['general_name'],
                'product_sku'  => $downloadable['general_sku']
            ),
            'downloadableOption' => array('title' => $linksTitle, 'optionTitle' => $link),
            'category'           => $returnCategory
        );
    }

    /**
     * Create Simple product
     *
     * @param bool $inSubCategory
     *
     * @return array
     */
    public function createSimpleProduct($inSubCategory = false)
    {
        //Create category
        if ($inSubCategory) {
            $category = $this->loadDataSet('Category', 'sub_category_required');
            $catPath = $category['parent_category'] . '/' . $category['name'];
            $this->navigate('manage_categories', false);
            $this->categoryHelper()->checkCategoriesPage();
            $this->categoryHelper()->createCategory($category);
            $this->assertMessagePresent('success', 'success_saved_category');
            $returnCategory = array('name' => $category['name'], 'path' => $catPath);
        } else {
            $returnCategory = array('name' => 'Default Category', 'path' => 'Default Category');
        }
        //Create product
        $assignCategory = array('general_categories' => $returnCategory['path']);
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $assignCategory);
        $this->navigate('manage_products');
        $this->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'simple'   => array(
                'product_name' => $simple['general_name'],
                'product_sku'  => $simple['general_sku']
            ),
            'category' => $returnCategory
        );
    }

    /**
     * Create Virtual product
     *
     * @param bool $inSubCategory
     *
     * @return array
     */
    public function createVirtualProduct($inSubCategory = false)
    {
        //Create category
        if ($inSubCategory) {
            $category = $this->loadDataSet('Category', 'sub_category_required');
            $catPath = $category['parent_category'] . '/' . $category['name'];
            $this->navigate('manage_categories', false);
            $this->categoryHelper()->checkCategoriesPage();
            $this->categoryHelper()->createCategory($category);
            $this->assertMessagePresent('success', 'success_saved_category');
            $returnCategory = array('name' => $category['name'], 'path' => $catPath);
        } else {
            $returnCategory = array('name' => 'Default Category', 'path' => 'Default Category');
        }
        //Create product
        $assignCategory = array('general_categories' => $returnCategory['path']);
        $virtual = $this->loadDataSet('Product', 'virtual_product_visible', $assignCategory);
        $this->navigate('manage_products');
        $this->createProduct($virtual, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'virtual'  => array(
                'product_name' => $virtual['general_name'],
                'product_sku'  => $virtual['general_sku']
            ),
            'category' => $returnCategory
        );
    }

    /**
     * Add new tab
     *
     * @param string $tabName
     * @return Core_Mage_Product_Helper
     */
    public function addTab($tabName)
    {
        array_push($this->productTabs, $tabName);

        return $this;
    }
}
