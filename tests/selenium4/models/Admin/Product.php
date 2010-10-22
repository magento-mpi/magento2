<?php

/**
 * Admin_Product model
 *
 * @author Magento Inc.
 */
class Model_Admin_Product extends Model_Admin
{

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();
        //<!-- This array Data For Positiv Tests -->
        $this->Data = array();
    }

    /**
     * Uncheck "Use Config Settings" on Inventory Tab and Gift Card Information
     * @param $path, $field, $i
     * Inventory Tab -> for fields "manage_stock" and "enable_qty_increments" set $i=2
     * and for each rest set $i=1.
     * Gift Card Information Tab -> set $i=3
     */
    public function uncheckUseDefault($params, $path, $field, $i)
    {
        $Data = $params ? $params : $this->Data;
        if (isset($Data[$field])) {
            if ($this->isElementPresent($this->getUiElement($path) . $this->getUiElement("inputs/ucheck_use_default", $i))) {
                $this->click($this->getUiElement($path) . $this->getUiElement("inputs/ucheck_use_default", $i));
            }
        }
    }

    /**
     * Select product settings
     *
     * @param array $params May contain the following params:
     * attrib_for_conf_prod, type
     */
    public function selectProductSettings($params)
    {
        $result = true;
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        $this->checkAndSelectField($params, "attribute_set", Null);
        $this->checkAndSelectField($params, "type", Null);
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts');
        $this->clickAndWait($this->getUiElement("buttons/addproductcontinue"));
        if ($Data["type"] == 'Configurable Product') {
            if (isset($Data["attrib_for_conf_prod"]) and
                    $this->isElementPresent(
                            $this->getUiElement("product/inputs/attribute_for_configurable_product",
                                    $Data["attrib_for_conf_prod"]))) {
                $this->click($this->getUiElement("product/inputs/attribute_for_configurable_product",
                                $Data["attrib_for_conf_prod"]));
                $this->clickAndWait($this->getUiElement("buttons/addproductcontinue"));
            } else {
                $this->setVerificationErrors("You cannot create a Configurable product");
                $result = false;
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
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        //Name
        $this->checkAndFillField($params, "name", Null);
        //Description
        $this->checkAndFillField($params, "description", Null);
        //Short Description
        $this->checkAndFillField($params, "short_description", Null);
        // SKU type, Weight type and Weight for Bundle Product
        if ($Data["type"] == 'Bundle Product') {
            $this->checkAndSelectField($params, "sku_type", Null);
            $this->checkAndSelectField($params, "weight_type", Null);
            if (isset($Data["weight_type"]) and $Data["weight_type"] == 'Fixed') {
                $this->checkAndFillField($params, "weight", Null);
            }
        }
        //SKU
        $this->checkAndFillField($params, "sku", Null);
        //Weight
        if ($Data["type"] == 'Simple Product' or $Data["type"] == 'Gift Card') {
            $this->checkAndFillField($params, "weight", Null);
        }
        //Set Product as New from Date
        $this->checkAndFillField($params, "news_from_date", Null);
        //Set Product as New to Date
        $this->checkAndFillField($params, "news_to_date", Null);
        //Status
        $this->checkAndSelectField($params, "status", Null);
        //URL key
        $this->checkAndFillField($params, "url_key", Null);
        //Visibility
        $this->checkAndSelectField($params, "visibility", Null);
        //Allow Gift Message
        $this->checkAndSelectField($params, "allow_gift_message", Null);
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
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        //Prices Tab
        $this->click($this->getUiElement("tabs/price"));
        //Price type, Price View and Price for Bundle product
        if ($Data["type"] == 'Bundle Product') {
            $this->checkAndSelectField($params, "price_type", Null);
            $this->checkAndSelectField($params, "price_view", Null);
            if (isset($Data["price_type"]) and $Data["price_type"] == 'Fixed') {
                $this->checkAndFillField($params, "price", Null);
            }
        }
        //Add values on Prices Tab for Simple, Virtual, Downloadable, Configurable products
        if ($Data["type"] != 'Gift Card' and
                $Data["type"] != 'Grouped Product') {
            if ($Data["type"] != 'Bundle Product') {
                //Price
                $this->checkAndFillField($params, "price", Null);
            }
            //Special Price
            $this->checkAndFillField($params, "special_price", Null);
            //Special Price From Date
            $this->checkAndFillField($params, "special_from_date", Null);
            //Special Price To Date
            $this->checkAndFillField($params, "special_to_date", Null);
            //Tax Class
            $this->checkAndSelectField($params, "tax_class", Null);
            if (isset($Data["tier_price_price"])) {
                for ($i = 0; $i <= count($Data['tier_price_price']) - 1; $i++) {
                    $this->click($this->getUiElement("buttons/add_tier_price"));
                    $this->type($this->getUiElement("inputs/tier_price_price", $i), $Data["tier_price_price"][$i]);
                    if (isset($Data["tier_price_qty"])) {
                        $this->type($this->getUiElement("inputs/tier_price_qty", $i), $Data["tier_price_qty"][$i]);
                    }
                }
            }
        }
        //Is product available for purchase with Google Checkout
        $this->checkAndSelectField($params, "enable_googlecheckout", Null);
        //Add Amount, Allow Open Amount, Open Amount Min Value and Open Amount Max Value for Gift Card
        if ($Data["type"] == 'Gift Card') {
            if (isset($Data["giftcard_amounts"])) {
                for ($i = 0; $i <= count($Data['giftcard_amounts']) - 1; $i++) {
                    $this->click($this->getUiElement("buttons/add_amount"));
                    $this->type($this->getUiElement("inputs/giftcard_amounts", $i), $Data["giftcard_amounts"][$i]);
                }
            }
            $this->checkAndSelectField($params, "allow_open_amount", Null);
            $this->checkAndFillField($params, "open_amount_max", Null);
            $this->checkAndFillField($params, "open_amount_min", Null);
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
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        $this->click($this->getUiElement("tabs/inventory"));
        //Manage Stock.
        $this->uncheckUseDefault($params, "selectors/manage_stock", "manage_stock", 2);
        $this->checkAndSelectField($params, "manage_stock", Null);
        if (isset($Data["manage_stock"]) and
                $Data["manage_stock"] == 'Yes') {
            if ($Data["type"] != 'Grouped Product' and
                    $Data["type"] != 'Configurable Product' and
                    $Data["type"] != 'Bundle Product') {
                //Qty
                $this->checkAndFillField($params, "inventory_qty", Null);
                //Qty for Item's Status to become Out of Stock
                $this->uncheckUseDefault($params, "inputs/inventory_min_qty", "inventory_min_qty", 1);
                $this->checkAndFillField($params, "inventory_min_qty", Null);
                //Backorders
                $this->uncheckUseDefault($params, "selectors/backorders", "backorders", 1);
                $this->checkAndSelectField($params, "backorders", Null);
                //Notify for Quantity Below
                $this->uncheckUseDefault($params, "inputs/notify_stock_qty", "notify_stock_qty", 1);
                $this->checkAndFillField($params, "notify_stock_qty", Null);
            }
            if ($Data["type"] == 'Simple Product' or $Data["type"] == 'Virtual Product') {
                //Qty Uses Decimals
                $this->checkAndSelectField($params, "is_qty_decimal", Null);
            }
            //Enable Qty Increments and Qty Increments
            $this->uncheckUseDefault($params, "selectors/enable_qty_increments", "enable_qty_increments", 2);
            $this->checkAndSelectField($params, "enable_qty_increments", Null);
            if (isset($Data["enable_qty_increments"]) and
                    isset($Data["qty_increments"]) and
                    $Data["enable_qty_increments"] == 'Yes') {
                $this->uncheckUseDefault($params, "inputs/qty_increments", "qty_increments", 1);
                $this->checkAndFillField($params, "qty_increments", Null);
            }
            //Stock Availability
            $this->checkAndSelectField($params, "stock_availability", Null);
        }
        if ($Data["type"] != 'Grouped Product' and
                $Data["type"] != 'Configurable Product' and
                $Data["type"] != 'Bundle Product') {
            //Minimum Qty Allowed in Shopping Cart
            $this->uncheckUseDefault($params, "inputs/min_sale_qty", "min_sale_qty", 1);
            $this->checkAndFillField($params, "min_sale_qty", Null);
            //Maximum Qty Allowed in Shopping Cart
            $this->uncheckUseDefault($params, "inputs/max_sale_qty", "max_sale_qty", 1);
            $this->checkAndFillField($params, "max_sale_qty", Null);
        }
    }

    /**
     * mark website on Websites Tab on product page
     *
     * @param array $params May contain the following params:
     * type, website_name
     *
     */
    public function fillWebsitesTab($params)
    {
        $Data = $params ? $params : $this->Data;
        if (isset($Data["website_name"])) {
            $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
            $this->click($this->getUiElement("tabs/websites"));
            $qtySite = $this->getXpathCount(
                            $this->getUiElement("inputs/website", $Data["website_name"]));
            if ($qtySite > 0) {
                if ($qtySite > 1) {
                    $this->printInfo("There are " . $qtySite . " sites with the name " . $Data["website_name"] . ". They will be selected all");
                }
                for ($i = 1; $i <= $qtySite; $i++) {
                    $mas = array($Data["website_name"], $i);
                    $this->click($this->getUiElement("inputs/website_many", $mas));
                }
            }
            if ($qtySite == 0) {
                $this->printInfo("Website with name '" . $Data["website_name"] . "' does not exist");
            }
        }
    }

    /**
     * mark category on Categories Tab on product page
     *
     * @param array $params May contain the following params:
     * type, category_name
     *
     */
    public function fillCategoriesTab($params)
    {
        $Data = $params ? $params : $this->Data;
        if (isset($Data["category_name"])) {
            $this->click($this->getUiElement("tabs/categories"));
            $this->pleaseWait();
            $qtyCategor = $this->getXpathCount(
                            $this->getUiElement("inputs/category", $Data["category_name"]));
            if ($qtyCategor == 0) {
                $this->printInfo("Category with name '" . $Data["category_name"] . "' does not exist");
            }
            if ($qtyCategor > 0) {
                $numNeededCat = array();
                for ($i = 1; $i <= $qtyCategor; $i++) {
                    $mas = array($Data["category_name"], $i);
                    $nameRoot = $this->getText($this->getUiElement("inputs/category_many", $mas));
                    $nameRoot = strstr(strrev($nameRoot), ' ');
                    $nameRoot = substr(strrev($nameRoot), 0, -1);
                    if ($nameRoot == $Data["category_name"]) {
                        $numNeededCat[] = "$i";
                    }
                }
                if (count($numNeededCat) == 0) {
                    $this->printInfo("Category with name '" . $Data["category_name"] . "' does not exist");
                }
                if (count($numNeededCat) > 0) {
                    if (count($numNeededCat) > 1) {
                        $this->printInfo("There are " . count($numNeededCat) . " categories with the name '" . $Data["category_name"] . "'. They will be selected all");
                    }
                    for ($j = 0; $j <= count($numNeededCat) - 1; $j++) {
                        $mas = array($Data["category_name"], $numNeededCat[$j]);
                        $this->click($this->getUiElement("inputs/category_many", $mas));
                    }
                }
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
     * downloadable_link_purchase_type, downloadable_link_price,
     * downloadable_link_max_downloads, downloadable_link_shareable,
     * downloadable_link_sample_url, downloadable_link_url,
     * downloadable_link_sort_order
     */
    public function fillDownloadInfTab($params)
    {
        $Data = $params ? $params : $this->Data;
        $this->click($this->getUiElement("tabs/downloadable_information"));
        // Samples
        // Samples Title
        $this->checkAndFillField($params, "downloadable_samples_title", Null);
        //Add new sample
        if (isset($Data['downloadable_sample_url']) or isset($Data['downloadable_sample_title'])) {
            $qtyDownSamples = $this->getXpathCount($this->getUiElement("elements/download_sample_container"));
            //Click Add New Row
            $this->click($this->getUiElement("buttons/add_sample_item"));
            //Title
            $this->checkAndFillField($params, "downloadable_sample_title", $qtyDownSamples);
            //File -URL
            $this->click($this->getUiElement("inputs/downloadable_sample_url_type", $qtyDownSamples));
            $this->checkAndFillField($params, "downloadable_sample_url", $qtyDownSamples);
            //Sort Order
            $this->checkAndFillField($params, "downloadable_sample_sort_order", $qtyDownSamples);
        }
        // Links
        // Links Title
        $this->checkAndFillField($params, "downloadable_links_title", Null);
        //Links can be purchased separately
        $this->checkAndSelectField($params, "downloadable_link_purchase_type", Null);
        //add new link
        if (isset($Data['downloadable_link_title']) or isset($Data['downloadable_link_url'])) {
            $qtyDownlinks = $this->getXpathCount($this->getUiElement("elements/download_link_container"));
            //Click Add New Row
            $this->click($this->getUiElement("buttons/add_link_item"));
            //Title
            $this->checkAndFillField($params, "downloadable_link_title", $qtyDownlinks);
            //Price
            if (isset($Data['downloadable_link_purchase_type']) and
                    $Data['downloadable_link_purchase_type'] == 'Yes') {
                $this->checkAndFillField($params, "downloadable_link_price", $qtyDownlinks);
            }
            //Max. Downloads
            $this->checkAndFillField($params, "downloadable_link_max_downloads", $qtyDownlinks);
            //Shareable
            $this->checkAndSelectField($params, "downloadable_link_shareable", $qtyDownlinks);
            //Sample - URL
            $this->click($this->getUiElement("inputs/downloadable_link_sample_url_type", $qtyDownlinks));
            $this->checkAndFillField($params, "downloadable_link_sample_url", $qtyDownlinks);
            //File - URL
            $this->click($this->getUiElement("inputs/downloadable_link_url_type", $qtyDownlinks));
            $this->checkAndFillField($params, "downloadable_link_url", $qtyDownlinks);
            //Sort Order
            $this->checkAndFillField($params, "downloadable_link_sort_order", $qtyDownlinks);
        }
    }

    /**
     * Filling "Associated Products" Tab on product page for Grouped and Configurable products
     *
     * @param array $params May contain the following params:
     * type, grouped_items_search_table, filter_sku,
     * grouped_items_sku, configurable_items_sku
     */
    public function fillAssociatedProductsTab($params)
    {
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        //Open Associated Products Tab
        $this->click($this->getUiElement("tabs/associated_products"));
        $this->pleaseWait();
        // Search and mark products
        if ($Data["type"] == 'Grouped Product') {
            $this->searchElement($params, "grouped_items_search_table", "filter_sku", "grouped_items_sku", "mark_filtered_product", NULL);
        }
        if ($Data["type"] == 'Configurable Product') {
            $this->searchElement($params, "configurable_items_search_table", "filter_sku", "configurable_items_sku", "mark_filtered_product", NULL);
        }
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
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        //Open Bundle Items Tab
        $this->click($this->getUiElement("tabs/bundle_items"));
        $this->pleaseWait();
        $this->checkAndSelectField($params, "bundle_shipment_type", Null);
        //Add new bundle option(s)
        if (isset($Data['bundle_options_title'])) {
            $qtyBundleOptions = $this->getXpathCount($this->getUiElement("elements/bundle_items_container"));
            $this->click($this->getUiElement("buttons/add_new_bundle_option"));
            //Default Title
            $this->checkAndFillField($params, "bundle_options_title", "$qtyBundleOptions");
            //Input Type
            $this->checkAndSelectField($params, "bundle_options_type", "$qtyBundleOptions");
            //Is Required
            $this->checkAndSelectField($params, "bundle_options_required", "$qtyBundleOptions");
            //Position
            $this->checkAndFillField($params, "bundle_options_position", "$qtyBundleOptions");
            if (isset($Data['bundle_items_sku'])) {
                //Add product(s)
                $this->click($this->getUiElement("buttons/bundle_option_add_product", "$qtyBundleOptions"));
                $this->pleaseWait();
                $this->searchElement($params, "bundle_items_search_table", "filter_sku", "bundle_items_sku", "mark_filtered_product", "$qtyBundleOptions");
                $this->click($this->getUiElement("buttons/bundle_option_add_product_confirm", "$qtyBundleOptions"));
            }
        }
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
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        //Gift Card Information Tab
        $this->click($this->getUiElement("tabs/gift_card_information"));
        //Card Type
        $this->checkAndSelectField($params, "giftcard_type", Null);
        //Is Redeemable
        $this->uncheckUseDefault($params, "selectors/giftcard_is_redeemable", "giftcard_is_redeemable", 3);
        $this->checkAndSelectField($params, "giftcard_is_redeemable", Null);
        //Lifetime (days)
        $this->uncheckUseDefault($params, "inputs/giftcard_lifetime", "giftcard_lifetime", 3);
        $this->checkAndFillField($params, "giftcard_lifetime", Null);
        //Allow Message
        $this->uncheckUseDefault($params, "selectors/giftcard_allow_message", "giftcard_allow_message", 3);
        $this->checkAndSelectField($params, "giftcard_allow_message", Null);
        //Email Template
        $this->uncheckUseDefault($params, "selectors/giftcard_email_template", "giftcard_email_template", 3);
        $this->checkAndSelectField($params, "giftcard_email_template", Null);
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
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/manageproducts"));
        $this->clickAndWait($this->getUiElement("buttons/addproduct"));
        if ($this->selectProductSettings($params)) {
            $this->fillGeneralTab($params);
            $this->fillPricesTab($params);
            $this->fillInvenoryTab($params);
            $this->fillWebsitesTab($params);
            $this->fillCategoriesTab($params);
            if ($Data["type"] == 'Gift Card') {
                $this->fillGiftCardInformTab();
            }
            if ($Data["type"] == 'Downloadable Product') {
                $this->fillDownloadInfTab($params);
            }
            if ($Data["type"] == 'Bundle Product') {
                $this->fillBundleItemsTab($params);
            }
            if ($Data["type"] == 'Grouped Product' or $Data["type"] == 'Configurable Product') {
                $this->fillAssociatedProductsTab($params);
            }
            $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
            $this->saveAndVerifyForErrors("save");
        }
    }

}