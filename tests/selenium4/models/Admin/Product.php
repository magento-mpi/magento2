<?php

/**
 * Admin_Product model
 *
 * @author Magento Inc.
 */
class Model_Admin_Product extends Model_Admin {

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();
        //<!-- This array Data For Positiv Tests -->
        $this->Data = array();
        $this->OrderModel = $this->getModel('admin/order');
    }

    /**
     * Uncheck 'Use Config Settings' on Inventory Tab and Gift Card Information
     * @param $path, $field, $i
     * Inventory Tab -> for fields 'manage_stock' and 'enable_qty_increments' set $i=2
     * and for each rest set $i=1.
     * Gift Card Information Tab -> set $i=3
     */
    public function uncheckUseDefault($params, $path, $field, $i)
    {
        $Data = $params ? $params : $this->Data;
        if (isset($Data[$field])) {
            if ($this->isElementPresent($this->getUiElement($path) . $this->getUiElement('inputs/ucheck_use_default', $i))) {
                $this->click($this->getUiElement($path) . $this->getUiElement('inputs/ucheck_use_default', $i));
            }
        }
    }

    /**
     * Select product settings
     *
     * @param array $params May contain the following params:
     * attrib_for_conf_prod, type
     */
    public function selectProductSettings($params, $type, $configAttribut)
    {
        $result = true;
        $this->setUiNamespace('admin/pages/catalog/manage_products/product');
        $this->checkAndSelectField($params, 'attribute_set');
        $result = $this->checkAndSelectField($params, 'type');
        $this->setUiNamespace('admin/pages/catalog/manage_products');
        $this->clickAndWait($this->getUiElement('buttons/addproductcontinue'));
        if ($type == 'Configurable Product') {
            if ($configAttribut != NULL) {
                $xpath = $this->getUiElement('product/inputs/attribute_for_configurable_product', $configAttribut);
                if ($this->isElementPresent($xpath)) {
                    $this->click($xpath);
                    $this->clickAndWait($this->getUiElement('buttons/addproductcontinue'));
                } else {
                    $this->setVerificationErrors('You cannot create a Configurable product');
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * Fill General Tab on product page
     *
     * @param array $params May contain the following params:
     * type, name, description, short_description, sku_type,
     * weight_type, weight, sku, news_from_date, news_to_date,
     * status, url_key, visibility, allow_gift_message
     *
     */
    public function fillGeneralTab($params)
    {
        $Data = $params ? $params : $this->Data;
        $type = $Data['type'];
        $this->setUiNamespace('admin/pages/catalog/manage_products/product');
        if ($this->isElementPresent($this->getUiElement('selectors/conf_attribute'))) {
            $this->select($this->getUiElement('selectors/conf_attribute'), $type);
        }
        //Name
        $this->checkAndFillField($params, 'name', Null);
        //Description
        $this->checkAndFillField($params, 'description', Null);
        //Short Description
        $this->checkAndFillField($params, 'short_description', Null);
        // SKU type, Weight type and Weight for Bundle Product
        if ($type == 'Bundle Product') {
            $this->checkAndSelectField($params, 'sku_type');
            $this->checkAndSelectField($params, 'weight_type');
            if ($this->isSetValue($params, 'weight_type') == 'Fixed') {
                $this->checkAndFillField($params, 'weight', Null);
            }
        }
        //SKU
        $this->checkAndFillField($params, 'sku', Null);
        //Weight
        if ($type == 'Simple Product' or $type == 'Gift Card') {
            $this->checkAndFillField($params, 'weight', Null);
        }
        //Set Product as New from Date
        $this->checkAndFillField($params, 'news_from_date', Null);
        //Set Product as New to Date
        $this->checkAndFillField($params, 'news_to_date', Null);
        //Status
        $this->checkAndSelectField($params, 'status');
        //URL key
        $this->checkAndFillField($params, 'url_key', Null);
        //Visibility
        $this->checkAndSelectField($params, 'visibility');
        //Allow Gift Message
        $this->checkAndSelectField($params, 'allow_gift_message');
    }

    /**
     * Fill Prices Tab on product page
     *
     * @param array $params May contain the following params:
     * type, price_type, price_view, price, special_price, special_from_date,
     * special_to_date, tax_class, enable_googlecheckout, giftcard_amounts,
     * allow_open_amount, open_amount_max, open_amount_min
     * 
     */
    public function fillPricesTab($params)
    {
        $Data = $params ? $params : $this->Data;
        $type = $Data['type'];
        $this->setUiNamespace('admin/pages/catalog/manage_products/product');
        //Prices Tab
        $this->click($this->getUiElement('tabs/price'));
        //Price type, Price View and Price for Bundle product
        if ($type == 'Bundle Product') {
            $this->checkAndSelectField($params, 'price_type');
            $this->checkAndSelectField($params, 'price_view');
            if ($this->isSetValue($params, 'price_type') == 'Fixed') {
                $this->checkAndFillField($params, 'price', Null);
            }
        }
        //Add values on Prices Tab for Simple, Virtual, Downloadable, Configurable products
        if ($type != 'Gift Card' and $type != 'Grouped Product') {
            if ($type != 'Bundle Product') {
                //Price
                $this->checkAndFillField($params, 'price', Null);
            }
            //Special Price
            $this->checkAndFillField($params, 'special_price', Null);
            //Special Price From Date
            $this->checkAndFillField($params, 'special_from_date', Null);
            //Special Price To Date
            $this->checkAndFillField($params, 'special_to_date', Null);
            //Tax Class
            $this->checkAndSelectField($params, 'tax_class');
            if (isset($Data['tier_price_price'])) {
                for ($i = 0; $i <= count($Data['tier_price_price']) - 1; $i++) {
                    $this->click($this->getUiElement('buttons/add_tier_price'));
                    $this->type($this->getUiElement('inputs/tier_price_price', $i), $Data['tier_price_price'][$i]);
                    if (isset($Data['tier_price_qty'])) {
                        $this->type($this->getUiElement('inputs/tier_price_qty', $i), $Data['tier_price_qty'][$i]);
                    }
                }
            }
        }
        //Is product available for purchase with Google Checkout
        $this->checkAndSelectField($params, 'enable_googlecheckout');
        //Add Amount, Allow Open Amount, Open Amount Min Value and Open Amount Max Value for Gift Card
        if ($type == 'Gift Card') {
            if (isset($Data['giftcard_amounts'])) {
                for ($i = 0; $i <= count($Data['giftcard_amounts']) - 1; $i++) {
                    $this->click($this->getUiElement('buttons/add_amount'));
                    $this->type($this->getUiElement('inputs/giftcard_amounts', $i), $Data['giftcard_amounts'][$i]);
                }
            }
            $this->checkAndSelectField($params, 'allow_open_amount');
            $this->checkAndFillField($params, 'open_amount_max', Null);
            $this->checkAndFillField($params, 'open_amount_min', Null);
        }
    }

    /**
     * Fill Inventory Tab on product page
     *
     * @param array $params May contain the following params:
     * type, manage_stock, inventory_qty, inventory_min_qty, backorders,
     * notify_stock_qty, is_qty_decimal, enable_qty_increments, qty_increments,
     * stock_availability, min_sale_qty, max_sale_qty
     * 
     */
    public function fillInvenoryTab($params)
    {
        $Data = $params ? $params : $this->Data;
        $type = $Data['type'];
        $this->setUiNamespace('admin/pages/catalog/manage_products/product');
        $this->click($this->getUiElement('tabs/inventory'));
        //Manage Stock.
        $this->uncheckUseDefault($params, 'selectors/manage_stock', 'manage_stock', 2);
        $this->checkAndSelectField($params, 'manage_stock');
        if ($this->isSetValue($params, 'manage_stock') == 'Yes') {
            if ($type != 'Grouped Product' and $type != 'Configurable Product' and $type != 'Bundle Product') {
                //Qty
                $this->checkAndFillField($params, 'inventory_qty', Null);
                //Qty for Item's Status to become Out of Stock
                $this->uncheckUseDefault($params, 'inputs/inventory_min_qty', 'inventory_min_qty', 1);
                $this->checkAndFillField($params, 'inventory_min_qty', Null);
                //Backorders
                $this->uncheckUseDefault($params, 'selectors/backorders', 'backorders', 1);
                $this->checkAndSelectField($params, 'backorders');
                //Notify for Quantity Below
                $this->uncheckUseDefault($params, 'inputs/notify_stock_qty', 'notify_stock_qty', 1);
                $this->checkAndFillField($params, 'notify_stock_qty', Null);
            }
            if ($type == 'Simple Product' or $type == 'Virtual Product') {
                //Qty Uses Decimals
                $this->checkAndSelectField($params, 'is_qty_decimal');
            }
            //Enable Qty Increments and Qty Increments
            $this->uncheckUseDefault($params, 'selectors/enable_qty_increments', 'enable_qty_increments', 2);
            $this->checkAndSelectField($params, 'enable_qty_increments');
            if (isset($Data['qty_increments']) and $this->isSetValue($params, 'enable_qty_increments') == 'Yes') {
                $this->uncheckUseDefault($params, 'inputs/qty_increments', 'qty_increments', 1);
                $this->checkAndFillField($params, 'qty_increments', Null);
            }
            //Stock Availability
            $this->checkAndSelectField($params, 'stock_availability');
        }
        if ($type != 'Grouped Product' and $type != 'Configurable Product' and $type != 'Bundle Product') {
            //Minimum Qty Allowed in Shopping Cart
            $this->uncheckUseDefault($params, 'inputs/min_sale_qty', 'min_sale_qty', 1);
            $this->checkAndFillField($params, 'min_sale_qty', Null);
            //Maximum Qty Allowed in Shopping Cart
            $this->uncheckUseDefault($params, 'inputs/max_sale_qty', 'max_sale_qty', 1);
            $this->checkAndFillField($params, 'max_sale_qty', Null);
        }
    }

    /**
     * mark website on Websites Tab on product page
     *
     * @param array $params May contain the following params:
     * type, website_name
     *
     */
    public function fillWebsitesTab($websiteName)
    {
        $this->setUiNamespace('admin/pages/catalog/manage_products/product');
        $this->click($this->getUiElement('tabs/websites'));
        $this->OrderModel->selectStore('website', $websiteName);
    }

    /**
     * mark category on Categories Tab on product page
     *
     * @param array $params May contain the following params:
     * type, category_name
     *
     */
    public function fillCategoriesTab($categoryName)
    {
        if (isset($categoryName)) {
            $this->click($this->getUiElement('tabs/categories'));
            $this->pleaseWait();
            $qtyCategor = $this->getXpathCount($this->getUiElement('inputs/category', $categoryName));
            if ($qtyCategor == 0) {
                $this->printInfo("Category with name '" . $categoryName . "' does not exist");
            }
            if ($qtyCategor > 0) {
                $numNeededCat = array();
                for ($i = 1; $i <= $qtyCategor; $i++) {
                    $mas = array($categoryName, $i);
                    $nameRoot = $this->getText($this->getUiElement('inputs/category_many', $mas));
                    $nameRoot = strstr(strrev($nameRoot), ' ');
                    $nameRoot = substr(strrev($nameRoot), 0, -1);
                    if ($nameRoot == $categoryName) {
                        $numNeededCat[] = "$i";
                    }
                }
                if (count($numNeededCat) == 0) {
                    $this->printInfo("Category with name '" . $categoryName . "' does not exist");
                }
                if (count($numNeededCat) > 0) {
                    if (count($numNeededCat) > 1) {
                        $this->printInfo("There are " . count($numNeededCat) . " categories with the name '"
                                . $categoryName . "'. They will be selected all");
                    }
                    for ($j = 0; $j <= count($numNeededCat) - 1; $j++) {
                        $mas = array($categoryName, $numNeededCat[$j]);
                        $this->click($this->getUiElement('inputs/category_many', $mas));
                    }
                }
            }
        }
    }

    /**
     * Add Sample Row for Downloadable product
     *
     * @param array $params
     * @param array $sampleArray
     */
    public function addSampleRow($params, $sampleArray)
    {
        $xpath = $this->getUiElement('elements/download_sample_container');
        $qtySampleRows = $this->getXpathCount($xpath);
        foreach ($sampleArray as $key => $value) {
            $numderRow = $qtySampleRows + 1;
            if (!$this->isElementPresent($xpath . "[$numderRow]")) {
                $this->click($this->getUiElement('buttons/add_sample_item'));
            }
            if ($key == 'downloadable_sample_url') {
                $this->click($this->getUiElement('inputs/downloadable_sample_url_type', $qtySampleRows));
            }
            $this->checkAndFillField($params, $key, $qtySampleRows);
        }
    }

    /**
     * Add Link Row for Downloadable product
     *
     * @param array $params
     * @param array $linkArray
     */
    public function addLinkRow($params, $linkArray)
    {
        $xpath = $this->getUiElement('elements/download_link_container');
        $qtyLinkRows = $this->getXpathCount($xpath);
        foreach ($linkArray as $key => $value) {
            $numderRow = $qtyLinkRows + 1;
            if (!$this->isElementPresent($xpath . "[$numderRow]")) {
                $this->click($this->getUiElement('buttons/add_link_item'));
            }
            if ($this->isSetValue($params, 'downloadable_links_purchase_type') == 'Yes' and
                    $key == 'downloadable_link_price') {
                $this->checkAndFillField($params, $key, $qtyLinkRows);
            } elseif ($key == 'downloadable_link_sample_url') {
                $this->click($this->getUiElement('inputs/downloadable_link_sample_url_type', $qtyLinkRows));
                $this->checkAndFillField($params, $key, $qtyLinkRows);
            } elseif ($key == 'downloadable_link_url') {
                $this->click($this->getUiElement('inputs/downloadable_link_url_type', $qtyLinkRows));
                $this->checkAndFillField($params, $key, $qtyLinkRows);
            } elseif ($key == 'downloadable_link_shareable') {
                $xpath = $this->getUiElement('selectors/downloadable_link_shareable', $qtyLinkRows);
                if ($this->isElementPresent($xpath . $this->getUiElement('/admin/global/elements/option_for_field', $value))) {
                    $this->select($xpath, 'label=' . $value);
                } else {
                    $this->printInfo("The value '" . $value . "' cannot be set for the field '" . $xpath . "'");
                }
            } else {
                $this->checkAndFillField($params, $key, $qtyLinkRows);
            }
        }
    }

    /**
     * Fill Downloadable Information Tab on product page for Downloadable Product
     *
     * @param array $params May contain the following params:
     * type, downloadable_samples_title, downloadable_sample_url,
     * downloadable_sample_url_type, downloadable_sample_sort_order,
     * downloadable_link_title, downloadable_link_url, downloadable_link_url,
     * downloadable_links_purchase_type, downloadable_link_price,
     * downloadable_link_max_downloads, downloadable_link_shareable,
     * downloadable_link_sample_url, downloadable_link_url,
     * downloadable_link_sort_order
     */
    public function fillDownloadInfTab($params)
    {
        $Data = $params ? $params : $this->Data;
        $this->click($this->getUiElement('tabs/downloadable_information'));
        // Samples
        $this->checkAndFillField($params, 'downloadable_samples_title', Null);
        $searchWord = '/^downloadable_sample_/';
        $sampleArray = $this->dataPreparation($params, $searchWord);
        $this->addSampleRow($params, $sampleArray);
        // Links
        $this->checkAndFillField($params, 'downloadable_links_title', Null);
        $this->checkAndSelectField($params, 'downloadable_links_purchase_type');
        $searchWord = '/^downloadable_link_/';
        $linkArray = $this->dataPreparation($params, $searchWord);
        $this->addLinkRow($params, $linkArray);
    }

    /**
     * Filling 'Associated Products' Tab on product page for Grouped and Configurable products
     *
     * @param array $params May contain the following params:
     * type, grouped_items_search_table, filter_sku,
     * grouped_items_sku, configurable_items_sku
     */
    public function fillAssociatedProductsTab($params)
    {
        $Data = $params ? $params : $this->Data;
        $type = $Data['type'];
        $this->setUiNamespace('admin/pages/catalog/manage_products/product');
        //Open Associated Products Tab
        $this->click($this->getUiElement('tabs/associated_products'));
        $this->pleaseWait();
        // Search and mark products
        $searchElements = $this->dataPreparation($params, '/^search_product_/');
        switch ($type) {
            case 'Grouped Product':
                $this->multiRunSearch('grouped_items_search_table', $searchElements, NULL);
                break;
            case 'Configurable Product':
                $this->multiRunSearch('configurable_items_search_table', $searchElements, NULL);
                break;
        }
    }

    /**
     * Add Bundle Option
     *
     * @param <type> $params
     * @param <type> $bundleOption
     * @param <type> $searchElements
     */
    public function addBundleOption($params, $bundleOption, $searchElements)
    {
        $xpath = $this->getUiElement('elements/bundle_items_container');
        $qtyBundleOptions = $this->getXpathCount($xpath);
        $numberItem = $qtyBundleOptions + 1;
        foreach ($bundleOption as $key => $value) {
            if (!$this->isElementPresent($xpath . "[$numberItem]")) {
                $this->click($this->getUiElement('buttons/add_new_bundle_option'));
            }
            switch ($key) {
                case 'bundle_options_type': case 'bundle_options_required':
                    $xpath = $this->getUiElement('selectors/' . $key, $qtyBundleOptions);
                    if ($this->isElementPresent($xpath . $this->getUiElement('/admin/global/elements/option_for_field', $value))) {
                        $this->select($xpath, 'label=' . $value);
                    } else {
                        $this->printInfo("The value '" . $value . "' cannot be set for the field '" . $xpath . "'");
                    }
                    break;
                case 'bundle_options_position': case 'bundle_options_title':
                    $this->checkAndFillField($params, $key, "$qtyBundleOptions");
                    break;
            }
        }
        if (!$this->isElementPresent($xpath . "[$numberItem]")) {
            $this->click($this->getUiElement('buttons/add_new_bundle_option'));
        }
        $this->click($this->getUiElement('buttons/bundle_option_add_product', "$qtyBundleOptions"));
        $this->pleaseWait();
        $this->multiRunSearch('bundle_items_container', $searchElements, "$qtyBundleOptions");
        $this->click($this->getUiElement('buttons/bundle_option_add_product_confirm', "$qtyBundleOptions"));
    }

    /**
     * Fill Bundle Items Tab on product page for bundle product
     *
     * @param array $params May contain the following params:
     * type,
     */
    public function fillBundleItemsTab($params)
    {
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/manage_products/product');
        //Open Bundle Items Tab
        $this->click($this->getUiElement('tabs/bundle_items'));
        $this->pleaseWait();
        $this->checkAndSelectField($params, 'bundle_shipment_type');
        //Add new bundle option(s)
        $searchWord = '/bundle_options_/';
        $bundleOption = $this->dataPreparation($params, $searchWord);
        $searchWord = '/search_product_/';
        $searchElements = $this->dataPreparation($params, $searchWord);
        $this->addBundleOption($params, $bundleOption, $searchElements);
    }

    /**
     * Fill Gift Card Information Tab on product page for Gift Card product
     *
     * @param array $params May contain the following params:
     * type, giftcard_type, giftcard_is_redeemable, giftcard_lifetime,
     * giftcard_allow_message, giftcard_email_template
     */
    public function fillGiftCardInformTab($params)
    {
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/manage_products/product');
        //Gift Card Information Tab
        $this->click($this->getUiElement('tabs/gift_card_information'));
        //Card Type
        $this->checkAndSelectField($params, 'giftcard_type');
        //Is Redeemable
        $this->uncheckUseDefault($params, 'selectors/giftcard_is_redeemable', 'giftcard_is_redeemable', 3);
        $this->checkAndSelectField($params, 'giftcard_is_redeemable');
        //Lifetime (days)
        $this->uncheckUseDefault($params, 'inputs/giftcard_lifetime', 'giftcard_lifetime', 3);
        $this->checkAndFillField($params, 'giftcard_lifetime', Null);
        //Allow Message
        $this->uncheckUseDefault($params, 'selectors/giftcard_allow_message', 'giftcard_allow_message', 3);
        $this->checkAndSelectField($params, 'giftcard_allow_message');
        //Email Template
        $this->uncheckUseDefault($params, 'selectors/giftcard_email_template', 'giftcard_email_template', 3);
        $this->checkAndSelectField($params, 'giftcard_email_template');
    }

    /**
     * Create product
     *
     * @param array $params May contain the following params:
     * type,
     */
    public function doCreateProduct($params)
    {
        $Data = $params ? $params : $this->Data;
        $type = $this->isSetValue($params, 'type');
        $configAttribut = $this->isSetValue($params, 'attrib_for_conf_prod');
        $categoryName = $this->isSetValue($params, 'category_name');
        $websiteName = $this->isSetValue($params, 'website_name');
        $this->setUiNamespace('admin/pages/catalog/manage_products');
        $this->navigate('Catalog/Manage Products');
        $this->clickAndWait($this->getUiElement('buttons/addproduct'));
        if ($this->selectProductSettings($params, $type, $configAttribut)) {
            $this->fillGeneralTab($params);
            $this->fillPricesTab($params);
            $this->fillInvenoryTab($params);
            $this->fillWebsitesTab($websiteName);
            $this->fillCategoriesTab($categoryName);
            switch ($type) {
                case 'Gift Card':
                    $this->fillGiftCardInformTab($params);
                    break;
                case 'Downloadable Product':
                    $this->fillDownloadInfTab($params);
                    break;
                case 'Bundle Product':
                    $this->fillBundleItemsTab($params);
                    break;
                case 'Grouped Product': case 'Configurable Product':
                    $this->fillAssociatedProductsTab($params);
                    break;
            }
            $this->setUiNamespace('admin/pages/catalog/manage_products/product');
            $this->saveAndVerifyForErrors();
        }
    }

    /**
     * Delete product
     * @param <type> $params
     */
    public function doDeleteProduct($params)
    {
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/manage_products');
        $this->navigate('Catalog/Manage Products');
        $searchWord = '/search_product_/';
        $searchElements = $this->dataPreparation($params, $searchWord);
        $result = $this->searchAndDoAction('product_grid', $searchElements, 'open', NUll);
        if ($result) {
            $this->doDeleteElement();
        }
    }

}