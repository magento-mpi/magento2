<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Product_Helper extends Mage_Selenium_AbstractHelper
{
    #**************************************************************************************
    #*                                                    Frontend Helper Methods         *
    #**************************************************************************************
    /**
     * Open product on FrontEnd
     *
     * @param string $productName
     */
    public function frontOpenProduct($productName)
    {
        if (!is_string($productName)) {
            $this->fail('Wrong data to open a product');
        }
        $productUrl = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '-', $productName)), '-');
        $this->addParameter('productUrl', $productUrl);
        $this->addParameter('elementTitle', $productName);
        $this->frontend('product_page', false);
        $this->setCurrentPage($this->getCurrentLocationUimapPage()->getPageId());
        $this->addParameter('productName', $productName);
        $openedProductName = $this->getControlAttribute('pageelement', 'product_name', 'text');
        $this->assertEquals($productName, $openedProductName,
            "Product with name '$openedProductName' is opened, but should be '$productName'");
    }

    public function frontOpenProductById($productId, $productName = '')
    {
        if (!is_string($productId)) {
            $this->fail('Wrong data to open a product');
        }
        $this->addParameter('id', $productId);
        $this->addParameter('elementTitle', $productName);
        $this->frontend('product_page_id', false);
        $this->setCurrentPage($this->getCurrentLocationUimapPage()->getPageId());
        $this->addParameter('productName', $productName);
        $openedProductName = $this->getControlAttribute('pageelement', 'product_name', 'text');
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
        $openedProductName = $this->getControlAttribute('pageelement', 'product_name', 'text');
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
    public function frontVerifyProductInfo(array $productData)
    {
        $this->frontOpenProduct($productData['general_name']);
        $xpathArray = $this->frontGetProductInfo();
        foreach ($xpathArray as $fieldName => $data) {
            if (is_string($data)) {
                if (!$this->elementIsPresent($data)) {
                    $this->addVerificationMessage('Could not find element ' . $fieldName);
                }
            } else {
                foreach ($data as $optionData) {
                    foreach ($optionData as $x => $y) {
                        if (!preg_match('/xpath/', $x)) {
                            continue;
                        }
                        if (!$this->elementIsPresent($y)) {
                            $this->addVerificationMessage(
                                'Could not find element type "' . $optionData['type'] . '" and title "'
                                . $optionData['title'] . '"');
                        }
                    }
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    public function frontGetProductInfo()
    {
        //@TODO
        return array();
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
        return $this->getProductDataFromGrid($productSearchData, 'Attrib. Set Name');
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
        $productXpath = $this->search($productSearchData, 'product_grid');
        return !is_null($productXpath);
    }

    /**
     * Open product.
     *
     * @param array $productSearch
     */
    public function openProduct(array $productSearch)
    {
        $productSearch = $this->_prepareDataForSearch($productSearch);
        $xpathTR = $this->search($productSearch, 'product_grid');
        $this->assertNotNull($xpathTR, 'Product is not found');
        $cellId = $this->getColumnIdByName('Name');
        $this->addParameter('tableLineXpath', $xpathTR);
        $this->addParameter('cellIndex', $cellId);
        $param = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
        $this->addParameter('elementTitle', $param);
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->clickControl('pageelement', 'table_line_cell_index');
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
        $this->fillProductInfo($productData, $productType);
        if ($isSave) {
            $this->saveForm('save');
        }
    }

    /**
     * Fill Product info
     *
     * @param array $productData
     * @param string $productType
     */
    public function fillProductInfo(array $productData, $productType = 'simple')
    {
        $this->fillProductTab($productData);
        $this->fillProductTab($productData, 'prices');
        $this->fillProductTab($productData, 'meta_information');
        //@TODO Fill in Images Tab
        if ($productType == 'simple' || $productType == 'virtual') {
            $this->fillProductTab($productData, 'recurring_profile');
        }
        $this->fillProductTab($productData, 'design');
        $this->fillProductTab($productData, 'gift_options');
        $this->fillProductTab($productData, 'inventory');
        $this->fillProductTab($productData, 'websites');
        $this->fillProductTab($productData, 'related');
        $this->fillProductTab($productData, 'up_sells');
        $this->fillProductTab($productData, 'cross_sells');
        $this->fillProductTab($productData, 'custom_options');
        if ($productType == 'grouped') {
            $this->fillProductTab($productData, 'associated');
        }
        if ($productType == 'bundle') {
            $this->fillProductTab($productData, 'bundle_items');
        }
        if ($productType == 'downloadable') {
            $this->fillProductTab($productData, 'downloadable_information');
        }
    }

    public function verifyProductInfo(array $productData, $skipElements = array(), $productType = 'simple')
    {
        $this->verifyPricesTab($productData);
        $this->verifyPricesTab($productData, 'prices');
        $this->verifyPricesTab($productData, 'meta_information');
        //@TODO Fill in Images Tab
        if ($productType == 'simple' || $productType == 'virtual') {
            $this->verifyPricesTab($productData, 'recurring_profile');
        }
        $this->verifyPricesTab($productData, 'design');
        $this->verifyPricesTab($productData, 'gift_options');
        $this->verifyPricesTab($productData, 'inventory');
        $this->verifyPricesTab($productData, 'websites');
        $this->verifyPricesTab($productData, 'related');
        $this->verifyPricesTab($productData, 'up_sells');
        $this->verifyPricesTab($productData, 'cross_sells');
        $this->verifyPricesTab($productData, 'custom_options');
        if ($productType == 'grouped') {
            $this->verifyPricesTab($productData, 'associated');
        }
        if ($productType == 'bundle') {
            $this->verifyPricesTab($productData, 'bundle_items');
        }
        if ($productType == 'downloadable') {
            $this->verifyPricesTab($productData, 'downloadable_information');
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
        $this->fillFieldset($forAttributesTab, 'attributes');
        $this->fillFieldset($forInventoryTab, 'inventory');
        $this->fillFieldset($forWebsitesTab, 'add_product');
    }

    /**
     * Change attribute set
     *
     * @param string $newAttributeSet
     */
    public function changeAttributeSet($newAttributeSet)
    {
        $this->clickButton('change_attribute_set', false);
        $this->waitForElementEditable($this->_getControlXpath('dropdown', 'choose_attribute_set'));
        $this->fillDropdown('choose_attribute_set', $newAttributeSet);
        $param = $this->getControlAttribute('dropdown', 'choose_attribute_set', 'selectedValue');
        $this->addParameter('setId', $param);
        $this->clickButton('apply');
        $this->addParameter('attributeSet', $newAttributeSet);
        $this->waitForElement($this->_getControlXpath('pageelement', 'product_attribute_set'));
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
     * @param array $productData
     * @param string $tabName Value - general|prices|meta_information|images|recurring_profile
     * |design|gift_options|inventory|websites|related|up_sells
     * |cross_sells|custom_options|bundle_items|associated|downloadable_information
     *
     * @return bool
     */
    public function fillProductTab(array $productData, $tabName = 'general')
    {
        $tabData = array();
        foreach ($productData as $key => $value) {
            if (preg_match('/^' . $tabName . '/', $key)) {
                $tabData[$key] = $value;
            }
        }
        if (empty($tabData)) {
            return;
        }

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
            case 'associated':
                $this->openTab($tabName);
                foreach ($tabData as $value) {
                    $this->assignProduct($value, $tabName);
                }
                break;
            case 'custom_options':
                $this->openTab($tabName);
                foreach ($tabData['custom_options_data'] as $value) {
                    $this->addCustomOption($value);
                }
                break;
            case 'bundle_items':
                $this->fillBundleItemsTab($tabData);
                break;
            case 'downloadable_information':
                $this->fillDownloadableInformationTab($tabData);
                break;
            default:
                $this->openTab($tabName);
                $this->fillTab($tabData, $tabName);
                $this->fillUserAttributesOnTab($tabData, $tabName);
                break;
        }
    }

    /**
     * Verify product info
     *
     * @param array $productData
     * @param string $tabName
     */
    public function verifyProductTab(array $productData, $tabName = 'general')
    {
        $tabData = array();
        foreach ($productData as $key => $value) {
            if (preg_match('/^' . $tabName . '/', $key)) {
                $tabData[$key] = $value;
            }
        }
        if (empty($tabData)) {
            return;
        }

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
            case 'associated':
                $this->openTab($tabName);
                foreach ($tabData as $value) {
                    $this->isAssignedProduct($value, $tabName);
                }
                break;
            case 'custom_options':
                $this->openTab($tabName);
                $this->verifyCustomOptions($tabData['custom_options_data']);
                break;
            case 'bundle_items':
                $this->verifyBundleOptions($tabData);
                break;
            case 'downloadable_information':
                $this->verifyDownloadableInformationTab($tabData);
                break;
            default:
                $this->openTab($tabName);
                $this->verifyForm($tabData, $tabName);
                $this->fillUserAttributesOnTab($tabData, $tabName);
                break;
        }
    }

    #*********************************************************************************
    #*                                               General Tab Helper Methods      *
    #*********************************************************************************
    public function fillGeneralTab($generalTab)
    {
        $this->openTab('general');
        if (isset($generalTab['general_configurable_attribute_title'])) {
            $this->fillConfigurableSettings($generalTab['general_configurable_attribute_title']);
            unset($generalTab['general_configurable_attribute_title']);
        }
        if (isset($generalTab['general_configurable_data'])) {
            $this->assignConfigurableVariations($generalTab['general_configurable_data']);
            unset($generalTab['general_configurable_data']);
        }
        if (isset($generalTab['general_categories'])) {
            $this->selectProductCategories($generalTab['general_categories']);
            unset($generalTab['general_categories']);
        }
        $this->fillTab($generalTab, 'general');
        $this->fillUserAttributesOnTab($generalTab, 'general');
    }

    public function verifyGeneralTab($generalTab)
    {
        $this->openTab('general');
        if (isset($generalTab['general_categories'])) {
            $this->isSelectedCategory($generalTab['general_categories']);
            unset($generalTab['general_categories']);
        }
        //@TODO
    }

    /**
     * @param string|array $categoryPath
     */
    public function selectProductCategories($categoryPath)
    {
        if (is_string($categoryPath)) {
            $categoryPath = explode(',', $categoryPath);
            $categoryPath = array_map('trim', $categoryPath);
        }
        $selectedNames = array();
        $locator = $this->_getControlXpath('field', 'general_categories');
        $element = $this->waitForElementEditable($locator, 10);
        $script = 'Element.prototype.documentOffsetTop = function()'
                  . '{return this.offsetTop + (this.offsetParent ? this.offsetParent.documentOffsetTop() : 0);};'
                  . 'var element = document.getElementsByClassName("category-selector-choices");'
                  . 'var top = element[0].documentOffsetTop() - (window.innerHeight / 2);'
                  . 'element[0].focus();window.scrollTo( 0, top );';
        $this->execute(array('script' => $script, 'args' => array()));
        $isSelected = $this->elementIsPresent($this->_getControlXpath('fieldset', 'chosen_category'));
        if ($isSelected) {
            foreach ($this->getChildElements($isSelected, 'div') as $el) {
                /** @var PHPUnit_Extensions_Selenium2TestCase_Element $el */
                $selectedNames[] = trim($el->text());
            }
        }
        foreach ($categoryPath as $category) {
            $explodeCategory = explode('/', $category);
            $categoryName = end($explodeCategory);
            if (!in_array($categoryName, $selectedNames)) {
                $this->addParameter('categoryPath', $category);
                $element->value($categoryName);
                $this->waitForElementEditable($this->_getControlXpath('link', 'category'))->click();
            }
            $this->addParameter('categoryName', $categoryName);
            $this->assertTrue($this->controlIsVisible('link', 'delete_category'), 'Category is not selected');
        }
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
        $isSelected = $this->elementIsPresent($this->_getControlXpath('fieldset', 'chosen_category'));
        if ($isSelected) {
            foreach ($this->getChildElements($isSelected, 'div') as $el) {
                /** @var PHPUnit_Extensions_Selenium2TestCase_Element $el */
                $selectedNames[] = trim($el->text());
            }
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
     * @param string|array $attributes
     */
    public function fillConfigurableSettings($attributes)
    {
        $this->fillCheckbox('is_configurable', 'Yes');
        if (is_string($attributes)) {
            $attributes = explode(',', $attributes);
            $attributes = array_map('trim', $attributes);
        }
        $attributesId = array();
        foreach ($attributes as $attributeTitle) {
            $this->selectConfigurableAttribute($attributeTitle);
        }
        $attributesUrl = urlencode(base64_encode(implode(',', $attributesId)));
        $this->addParameter('attributesUrl', $attributesUrl);
        $this->clickButton('generate_product_variations');
    }

    /**
     * Select configurable attribute on Product page using searchable attribute selector control
     *
     * @param string $attributeTitle
     */
    public function selectConfigurableAttribute($attributeTitle)
    {
        $locator = $this->_getControlXpath('field', 'attribute_selector');
        $element = $this->waitForElementEditable($locator, 10);
        $element->value($attributeTitle);
        $this->addParameter('attributeName', $attributeTitle);
        $this->waitForElementEditable($this->_getControlXpath('link', 'suggested_attribute'))->click();
    }

    public function assignConfigurableVariations()
    {
        if (!$this->controlIsPresent('checkbox', 'is_configurable')
            || !$this->getControlAttribute('checkbox', 'is_configurable', 'selectedValue')
        ) {

        }
        //@TODO
    }

    /**
     * Unassign all associated products in configurable product
     */
    public function unassignAllConfigurableVariations()
    {
        if (!$this->controlIsPresent('checkbox', 'is_configurable')
            || !$this->getControlElement('checkbox', 'is_configurable')->selected()
        ) {
            return;
        }
        $variationsCount = $this->getControlCount('pageelement', 'variation_line');
        while ($variationsCount > 0) {
            $this->addParameter('rowNum', $variationsCount--);
            $this->fillCheckbox('include_variation', 'No');
        }
    }

    /**
     * Check variation matrix combinations
     *
     * @param array $matrixData
     * @param bool $isAssignedData
     */
    public function verifyConfigurableVariations(array $matrixData, $isAssignedData = false)
    {
        $rowElements = $this->getControlElements('pageelement', 'variation_line');
        if (count($rowElements) != count($matrixData)) {
            $this->fail('Not all variations are represented in variation matrix');
        }
        /** @var $rowElement PHPUnit_Extensions_Selenium2TestCase_Element */
        /** @var $tdElement PHPUnit_Extensions_Selenium2TestCase_Element */
        $data = array();
        foreach ($rowElements as $key => $rowElement) {
            $this->addParameter('rowNum', $key + 1);
            if ($isAssignedData != $this->getControlAttribute('checkbox', 'include_variation', 'selectedValue')) {
                $this->addVerificationMessage(
                    'Checkbox in ' . $key + 1 . ' field ' . (($isAssignedData) ? 'is not' : 'is') . ' selected');
            }
            $tdElements = $this->getChildElements($rowElement, 'td');
            foreach ($tdElements as $keyTd => $tdElement) {
                $data[$key + 1][$keyTd + 1] = trim(str_replace('Choose', '', $tdElement->text()));
            }
            $data[$key + 1] = array_diff($data[$key + 1], array(''));
        }
        $this->assertEquals($matrixData, $data);
        $this->assertEmptyVerificationErrors();
    }

    #*********************************************************************************
    #*                                               Prices Tab Helper Methods       *
    #*********************************************************************************
    public function fillPricesTab($pricesTab)
    {
        $this->openTab('prices');
        if (isset($pricesTab['prices_tier_price_data'])) {
            foreach ($pricesTab['prices_tier_price_data'] as $value) {
                $this->addTierPrice($value);
            }
            unset($pricesTab['prices_tier_price_data']);
        }
        if (isset($pricesTab['prices_group_price_data'])) {
            foreach ($pricesTab['prices_group_price_data'] as $value) {
                $this->addTierPrice($value);
            }
            unset($pricesTab['prices_group_price_data']);
        }
        $this->fillTab($pricesTab, 'prices');
        $this->fillUserAttributesOnTab($pricesTab, 'prices');
    }

    public function verifyPricesTab($pricesTab)
    {
        $this->openTab('prices');
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
        $rowNumber = $this->getControlCount('fieldset', 'tier_price_row');
        $this->addParameter('tierPriceId', $rowNumber);
        $this->clickButton('add_tier_price', false);
        if (isset($tierPriceData['prices_tier_price_website'])
            && !$this->controlIsVisible('dropdown', 'prices_tier_price_website')
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
        $rowQty = $this->getControlCount('fieldset', 'tier_price_row');
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
                && !$this->controlIsVisible('dropdown', 'prices_tier_price_website')
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
        $rowNumber = $this->getControlCount('fieldset', 'group_price_row');
        $this->addParameter('groupPriceId', $rowNumber);
        $this->clickButton('add_group_price', false);
        $this->fillForm($groupPriceData, 'prices');
    }

    public function verifyGroupPrices(array $groupPriceData)
    {
        //@TODO
    }

    #*********************************************************************************
    #*                          Websites Tab Helper Methods                          *
    #*********************************************************************************
    public function fillWebsitesTab($websiteData)
    {
        if (!$this->controlIsPresent('tab', 'websites') && $websiteData == 'Main Website') {
            return;
        }
        $this->openTab('websites');
        $websites = explode(',', $websiteData);
        $websites = array_map('trim', $websites);
        foreach ($websites as $website) {
            $this->addParameter('websiteName', $website);
            $this->assertTrue($this->controlIsPresent('checkbox', 'websites'),
                'Website with name "' . $website . '" does not exist');
            $this->fillCheckbox('websites', 'Yes');
        }
    }

    public function verifyWebsitesTab($websiteData)
    {
        if (!$this->controlIsPresent('tab', 'websites') && $websiteData == 'Main Website') {
            return;
        }
        $this->openTab('websites');
        $websites = explode(',', $websiteData);
        $websites = array_map('trim', $websites);
        foreach ($websites as $website) {
            $this->addParameter('websiteName', $website);
            $this->assertTrue($this->controlIsPresent('checkbox', 'websites'),
                'Website with name "' . $website . '" does not exist');
            if (!$this->getControlAttribute('checkbox', 'websites', 'selectedValue')) {
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
        $fillingData = array();
        foreach ($data as $key => $value) {
            if (!preg_match('/^' . $fieldSetName . '_search_/', $key)) {
                $fillingData[$key] = $value;
                unset($data[$key]);
            }
        }

        $xpathTR = $this->search($data, $fieldSetName);
        if (is_null($xpathTR)) {
            $this->addVerificationMessage(
                $fieldSetName . " tab: Product is not assigned with data: \n" . print_r($data, true));
        } elseif ($fillingData) {
            $fieldsetXpath = $this->_getControlXpath('fieldset', $fieldSetName);
            $this->addParameter('productXpath', str_replace($fieldsetXpath, '', $xpathTR));
            $this->verifyForm($fillingData, $fieldSetName);
        }
    }

    /**
     * Unselect any associated product(as up_sells, cross_sells, related) to opened product
     *
     * @param string $type
     * @param bool $saveChanges
     */
    public function unselectAssociatedProduct($type, $saveChanges = false)
    {
        $this->openTab($type);
        $this->addParameter('tableXpath', $this->_getControlXpath('fieldset', $type));
        if (!$this->controlIsPresent('message', 'specific_table_no_records_found')) {
            $this->fillCheckbox($type . '_select_all', 'No');
            if ($saveChanges) {
                $this->saveAndContinueEdit('button', 'save_and_continue_edit');
                $this->assertTrue($this->controlIsPresent('message', 'specific_table_no_records_found'),
                    'There are products assigned to "' . $type . '" tab');
            }
        }
    }

    #*********************************************************************************
    #*                      Custom Options' Tab Helper Methods                       *
    #*********************************************************************************
    public function fillCustomOptionsTab($customOptionsTab)
    {

    }

    /**
     * Add Custom Option
     *
     * @param array $customOptionData
     */
    public function addCustomOption(array $customOptionData)
    {
        $optionId = $this->getControlCount('fieldset', 'custom_option_set') + 1;
        $this->addParameter('optionId', $optionId);
        $this->clickButton('add_option', false);
        $this->fillForm($customOptionData, 'custom_options');
        foreach ($customOptionData as $rowKey => $rowValue) {
            if (preg_match('/^custom_option_row/', $rowKey) && is_array($rowValue)) {
                $rowId = $this->getControlCount('pageelement', 'custom_option_row');
                $this->addParameter('rowId', $rowId);
                $this->clickButton('add_row', false);
                $this->fillForm($rowValue, 'custom_options');
            }
        }
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
        $this->openTab('custom_options');
        $optionsQty = $this->getControlCount('fieldset', 'custom_option_set');
        $needCount = count($customOptionData);
        if ($needCount != $optionsQty) {
            $this->addVerificationMessage(
                'Product must be contains ' . $needCount . ' Custom Option(s), but contains ' . $optionsQty);
            return false;
        }
        $numRow = 1;
        foreach ($customOptionData as $value) {
            if (is_array($value)) {
                $optionId = $this->getCustomOptionIdByRow($numRow);
                $this->addParameter('optionId', $optionId);
                $this->verifyForm($value, 'custom_options');
                $numRow++;
            }
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
        $optionElements = $this->getElements('fieldset', 'custom_option_set');
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
     * Get Custom Option Id By Title
     *
     * @param string
     *
     * @return integer
     */
    public function getCustomOptionIdByTitle($optionTitle)
    {
        $optionElements = $this->getElements('fieldset', 'custom_option_set');
        /** @var $optionElement PHPUnit_Extensions_Selenium2TestCase_Element */
        foreach ($optionElements as $optionElement) {
            $optionTitle = $this->getChildElements($optionElement, "//input[@value='{$optionTitle}']", false);
            if (!empty($optionTitle)) {
                $elementId = $optionTitle[0]->attribute('id');
                foreach (explode('_', $elementId) as $value) {
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
        $this->openTab('custom_options');
        $this->clickButton('import_options', false);
        $this->waitForElementVisible($this->_getControlXpath('fieldset', 'select_product_custom_option'));
        foreach ($productData as $value) {
            $this->searchAndChoose($value, 'select_product_custom_option_grid');
        }
        $this->clickButton('import', false);
        $this->pleaseWait();
    }

    /**
     * Delete all custom options
     */
    public function deleteAllCustomOptions()
    {
        $this->openTab('custom_options');
        while ($this->controlIsPresent('fieldset', 'custom_option_set')) {
            $this->assertTrue($this->buttonIsPresent('button', 'delete_custom_option'),
                $this->locationToString() . "Problem with 'Delete Option' button.\n"
                . 'Control is not present on the page');
            $this->clickButton('delete_custom_option', false);
        }
    }

    /**
     * Delete all Custom Options
     *
     * @return void
     */
    public function deleteCustomOptions()
    {
        $this->openTab('custom_options');
        $customOptions = $this->getControlElements('fieldset', 'custom_option_set');
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $customOption */
        foreach ($customOptions as $customOption) {
            $optionId = '';
            $elementId = explode('_', $customOption->attribute('id'));
            foreach ($elementId as $id) {
                if (is_numeric($id)) {
                    $optionId = $id;
                }
            }
            $this->addParameter('optionId', $optionId);
            $this->clickButton('delete_option', false);
        }
    }

    #*********************************************************************************
    #*                  Downloadable Option Tab Helper Methods                       *
    #*********************************************************************************

    public function fillDownloadableInformationTab(array $downloadableData)
    {
        $this->openTab('downloadable_information');
        if (!$this->controlIsPresent('pageelement', 'opened_downloadable_sample')) {
            $this->clickControl('link', 'downloadable_sample', false);
        }
        if (!$this->controlIsPresent('pageelement', 'opened_downloadable_link')) {
            $this->clickControl('link', 'downloadable_link', false);
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

    public function verifyDownloadableInformationTab(array $downloadableData)
    {
        $this->openTab('downloadable_information');
        if (!$this->controlIsPresent('pageelement', 'opened_downloadable_sample')) {
            $this->clickControl('link', 'downloadable_sample', false);
        }
        if (!$this->controlIsPresent('pageelement', 'opened_downloadable_link')) {
            $this->clickControl('link', 'downloadable_link', false);
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
        $rowNumber = $this->getControlCount('pageelement', 'added_downloadable_' . $type);
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
        $this->openTab('downloadable_information');
        $rowQty = $this->getControlCount('pageelement', 'downloadable_' . $type . '_row');
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
        return true;
    }

    /**
     * Delete Samples/Links rows on Downloadable Information tab
     */
    public function deleteDownloadableInformation($type)
    {
        $this->openTab('downloadable_information');
        if (!$this->controlIsPresent('pageelement', 'opened_downloadable_' . $type)) {
            $this->clickControl('link', 'downloadable_' . $type, false);
        }
        $rowQty = $this->getControlCount('pageelement', 'added_downloadable_' . $type);
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

    public function fillBundleItemsTab($bundleItems)
    {
        $this->openTab('bundle_items');
        if (isset($bundleItems['ship_bundle_items'])) {
            $this->fillDropdown('ship_bundle_items', $bundleItems['ship_bundle_items']);
            unset($bundleItems['ship_bundle_items']);
        }
        foreach ($bundleItems as $value) {
            $this->addBundleOption($value);
        }
    }

    /**
     * Add Bundle Option
     *
     * @param array $bundleOptionData
     */
    public function addBundleOption(array $bundleOptionData)
    {
        $optionsCount = $this->getControlCount('pageelement', 'bundle_item_row');
        $this->addParameter('optionId', $optionsCount);
        $this->clickButton('add_new_option', false);
        $this->fillForm($bundleOptionData, 'bundle_items');
        foreach ($bundleOptionData as $value) {
            $productSearch = array();
            $selectionSettings = array();
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if ($k == 'bundle_items_search_name' or $k == 'bundle_items_search_sku') {
                        $this->addParameter('productSku', $v);
                    }
                    if (preg_match('/^bundle_items_search_/', $k)) {
                        $productSearch[$k] = $v;
                    } elseif ($k == 'bundle_items_qty_to_add') {
                        $selectionSettings['selection_item_default_qty'] = $v;
                    } elseif (preg_match('/^selection_item_/', $k)) {
                        $selectionSettings[$k] = $v;
                    }
                }
                if ($productSearch) {
                    $this->clickButton('add_selection', false);
                    $this->pleaseWait();
                    $this->searchAndChoose($productSearch, 'select_product_to_bundle_option');
                    $this->clickButton('add_selected_products', false);
                    if ($selectionSettings) {
                        $this->fillForm($selectionSettings);
                    }
                }
            }
        }
    }

    /**
     * verify Bundle Options
     *
     * @param array $bundleData
     *
     * @return boolean
     */
    public function verifyBundleOptions(array $bundleData)
    {
        $this->openTab('bundle_items');
        $optionsCount = $this->getControlCount('pageelement', 'bundle_item_grid');
        $needCount = count($bundleData);
        if (array_key_exists('ship_bundle_items', $bundleData)) {
            $needCount = $needCount - 1;
        }
        if ($needCount != $optionsCount) {
            $this->addVerificationMessage(
                'Product must be contains ' . $needCount . 'Bundle Item(s), but contains ' . $optionsCount);
            return false;
        }

        $identifier = 0;
        foreach ($bundleData as $option => $values) {
            if (is_string($values)) {
                $this->verifyForm(array($option => $values), 'bundle_items');
            }
            if (is_array($values)) {
                $this->addParameter('optionId', $identifier);
                $this->verifyForm($values, 'bundle_items');
                foreach ($values as $k => $v) {
                    if (preg_match('/^add_product_/', $k) && is_array($v)) {
                        $selectionSettings = array();
                        $productSku = '';
                        foreach ($v as $field => $data) {
                            if ($field == 'bundle_items_search_name' or $field == 'bundle_items_search_sku') {
                                $productSku = $data;
                            }
                            if (!preg_match('/^bundle_items_search/', $field)) {
                                if ($field == 'bundle_items_qty_to_add') {
                                    $selectionSettings['selection_item_default_qty'] = $data;
                                } else {
                                    $selectionSettings[$field] = $data;
                                }
                            }
                        }
                        $k = $identifier + 1;
                        $this->addParameter('productSku', $productSku);
                        $this->addParameter('index', $k);
                        if (!$this->controlIsPresent('pageelement', 'bundle_item_grid_index_product')) {
                            $this->addVerificationMessage("Product with sku(name)'" . $productSku . "
                                ' is not assigned to bundle item $identifier");
                        } else {
                            if ($selectionSettings) {
                                $this->addParameter('productSku', $productSku);
                                $this->verifyForm($selectionSettings, 'bundle_items');
                            }
                        }
                    }
                }
                $identifier++;
            }
        }
        return true;
    }

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
        $productCat = array('general_categories' => $returnCategory['path']);
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $configurableOptions = array($attrData['option_1']['store_view_titles']['Default Store View'],
                                     $attrData['option_2']['store_view_titles']['Default Store View'],
                                     $attrData['option_3']['store_view_titles']['Default Store View']);
        $attrCode = $attrData['attribute_code'];
        $associatedAttributes =
            $this->loadDataSet('AttributeSet', 'associated_attributes', array('General' => $attrCode));
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $productCat);
        $simple['general_user_attr']['dropdown'][$attrCode] = $attrData['option_1']['admin_option_name'];
        $virtual = $this->loadDataSet('Product', 'virtual_product_visible', $productCat);
        $virtual['general_user_attr']['dropdown'][$attrCode] = $attrData['option_2']['admin_option_name'];
        $download = $this->loadDataSet('SalesOrder', 'downloadable_product_for_order',
            array('downloadable_links_purchased_separately' => 'No', 'general_categories' => $returnCategory['path']));
        $download['general_user_attr']['dropdown'][$attrCode] = $attrData['option_3']['admin_option_name'];
        $configurable = $this->loadDataSet('SalesOrder', 'configurable_product_for_order',
            array('general_configurable_attribute_title' => $attrData['admin_title'],
                  'general_categories'                           => $returnCategory['path']),
            array('associated_1' => $simple['general_sku'], 'associated_2' => $virtual['general_sku'],
                  'associated_3' => $download['general_sku']));
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $this->navigate('manage_products');
        $this->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($virtual, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($download, 'downloadable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('simple' => array('product_name' => $simple['general_name'],
                                       'product_sku'  => $simple['general_sku']),
                     'downloadable' => array('product_name' => $download['general_name'],
                                             'product_sku'  => $download['general_sku']),
                     'virtual' => array('product_name' => $virtual['general_name'],
                                        'product_sku'  => $virtual['general_sku']),
                     'configurable' => array('product_name' => $configurable['general_name'],
                                             'product_sku'  => $configurable['general_sku']),
                     'simpleOption' => array('option'       => $attrData['option_1']['admin_option_name'],
                                             'option_front' => $configurableOptions[0]),
                     'virtualOption' => array('option'       => $attrData['option_2']['admin_option_name'],
                                              'option_front' => $configurableOptions[1]),
                     'downloadableOption' => array('option'       => $attrData['option_3']['admin_option_name'],
                                                   'option_front' => $configurableOptions[2]),
                     'configurableOption' => array('title'                  => $attrData['admin_title'],
                                                   'custom_option_dropdown' => $configurableOptions[0]),
                     'attribute' => array('title'       => $attrData['admin_title'],
                                          'title_front' => $attrData['store_view_titles']['Default Store View'],
                                          'code'        => $attrCode), 'category' => $returnCategory);
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
            array('associated_1' => $simple['general_sku'], 'associated_2' => $virtual['general_sku'],
                  'associated_3' => $download['general_sku']));
        $this->navigate('manage_products');
        $this->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($virtual, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($download, 'downloadable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($grouped, 'grouped');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('simple' => array('product_name' => $simple['general_name'],
                                       'product_sku'  => $simple['general_sku']),
                     'downloadable' => array('product_name' => $download['general_name'],
                                             'product_sku'  => $download['general_sku']),
                     'virtual' => array('product_name' => $virtual['general_name'],
                                        'product_sku'  => $virtual['general_sku']),
                     'grouped' => array('product_name' => $grouped['general_name'],
                                        'product_sku'  => $grouped['general_sku']), 'category' => $returnCategory,
                     'groupedOption' => array('subProduct_1' => $simple['general_name'],
                                              'subProduct_2' => $virtual['general_name'],
                                              'subProduct_3' => $download['general_name']));
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
            array('add_product_1' => $simple['general_sku'], 'price_product_1' => 0.99, 'price_product_2' => 1.24,
                  'add_product_2' => $virtual['general_sku']));
        $this->navigate('manage_products');
        $this->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($virtual, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->createProduct($bundle, 'bundle');
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('simple' => array('product_name' => $simple['general_name'],
                                       'product_sku'  => $simple['general_sku']),
                     'virtual' => array('product_name' => $virtual['general_name'],
                                        'product_sku'  => $virtual['general_sku']),
                     'bundle' => array('product_name' => $bundle['general_name'],
                                       'product_sku'  => $bundle['general_sku']), 'category' => $returnCategory,
                     'bundleOption' => array('subProduct_1' => $simple['general_name'],
                                             'subProduct_2' => $virtual['general_name'],
                                             'subProduct_3' => $simple['general_name'],
                                             'subProduct_4' => $virtual['general_name']));
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
        return array('downloadable' => array('product_name' => $downloadable['general_name'],
                                             'product_sku'  => $downloadable['general_sku']),
                     'downloadableOption' => array('title' => $linksTitle, 'optionTitle' => $link),
                     'category' => $returnCategory);
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
        return array('simple' => array('product_name' => $simple['general_name'],
                                       'product_sku'  => $simple['general_sku']), 'category' => $returnCategory);
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
        return array('virtual' => array('product_name' => $virtual['general_name'],
                                        'product_sku'  => $virtual['general_sku']), 'category' => $returnCategory);
    }
}
