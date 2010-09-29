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
    public function loadConfigData() {
        parent::loadConfigData();
          //<!-- This array Data For Positiv Tests -->
/*        $this->Data = array(
            //<!-- Product Settings tab -->
            'type_all'                              => array(
                                        'Simple Product',
                                        'Virtual Product',
                                        'Downloadable Product',
                                        'Grouped Product',
                                        'Bundle Product',
                                        'Configurable Product',
                                        'Gift Card',
                                        ),
            'type'                                  => 'Gift Card',
            'attribute_set'                         => 'smoke_attrSet',
            'attrib_for_conf_prod'                  => 'Admin Title',
            // <!-- Genral tab -->
            'sku_type_all'                          => array('Fixed', 'Dynamic'),
            'weight_type_all'                       => array('Fixed', 'Dynamic'),
            'status_all'                            => array('Enabled', 'Disabled'),
            'allow_gift_message_all'                => array('Yes', 'No', 'Use config'),
            'visibility_all'                        => array(
                                        'Catalog',
                                        'Search',
                                        'Catalog, Search',
                                        'Not Visible Individually',
                                        '-- Please Select --'
                                        ),
            'name'                                  => 'Simple Product',
            'description'                           => 'Product description',
            'short_description'                     => 'Product short description',
            'sku'                                   => 'SKU-' . rand(1, 100),
            'sku_type'                              => 'Dynamic',
            'weight'                                => rand(10, 100),
            'weight_type'                           => 'Dynamic',
            'news_from_date'                        => '09/20/10',
            'news_to_date'                          => '09/30/10',
            'status'                                => 'Enabled',
            //'url_key'                             => 'product-01',
            'visibility'                            => 'Catalog',
            'allow_gift_message'                    => 'Yes',
            // <!-- Prices tab -->
            'price_type_all'                        => array('Fixed', 'Dynamic'),
            'price_view_all'                        => array('As Low as', 'Price Range'),
            'price'                                 => rand(500, 900),
            'price_type'                            => 'Dynamic',
            'special_price'                         => rand(1, 600),
            'special_from_date'                     => '09/20/10',
            'special_to_date'                       => '09/30/10',
            'tier_price_price'                      => array(50, 99),
            'tier_price_qty'                        => array(10, 5),
            'tax_class'                             => 'Shipping',
            'enable_googlecheckout'                 => 'No',
            'price_view'                            => 'As Low as',
            'allow_open_amount'                     => 'Yes',
            'open_amount_min'                       => rand(1, 100),
            'open_amount_max'                       => rand(100, 200),
            'giftcard_amounts'                      => array(rand(50, 100), rand(100, 300), rand(500, 600), rand(1, 50)),
            // <!-- Inventory tab -->
            'backorders_all'                        => array(
                                        'Allow Qty Below 0',
                                        'No Backorders',
                                        'Allow Qty Below 0 and Notify Customer'
                                        ),
            'stock_availability_all'                => array('In Stock', 'Out of Stock'),
            'manage_stock'                          => 'Yes',
            'inventory_qty'                         => '1',
            'inventory_min_qty'                     => rand(0, 10),
            'min_sale_qty'                          => rand(1, 5),
            'max_sale_qty'                          => rand(90, 100),
            'is_qty_decimal'                        => 'Yes',
            'backorders'                            => 'Allow Qty Below 0',
            'notify_stock_qty'                      => rand(1, 5),
            'enable_qty_increments'                 => 'Yes',
            'qty_increments'                        => rand(1, 5),
            'stock_availability'                    => 'In Stock',
            // <!-- Website tab -->
            'website_name'                          => Core::getEnvConfig('backend/scope/site/name'),
            // <!-- Category tab -->
            'category_name'                         => 'st-subcat',
            // <!-- Gift Card Information tab -->
            'giftcard_type_all'                     => array('Virtual', 'Physical', 'Combined'),
            'giftcard_type'                         => 'Combined',
            'giftcard_is_redeemable'                => 'No',
            'giftcard_lifetime'                     => rand(1, 5),
            'giftcard_allow_message'                => 'No',
            //'giftcard_email_template'             => '',
            // <!-- Downloadable Information tab -->
            'downloadable_samples_title'            => 'samples_title',
            'downloadable_sample_title'             => 'sample_title_1',
            'downloadable_sample_url'               => 'http://sample_url_1',
            'downloadable_sample_sort_order'        => 1,
            'downloadable_links_title'              => 'links_title',
            'downloadable_link_purchase_type'       => 'No',
            'downloadable_link_title'               => 'link_title_1',
            'downloadable_link_price'               => rand(100, 500),
            'downloadable_link_max_downloads'       => rand(1, 5),
            'downloadable_link_shareable'           => 'No',
            'downloadable_link_sample_url'          => 'http://link_sample_url_1',
            'downloadable_link_url'                 => 'http://link_url_1',
            'downloadable_link_sort_order'          => rand(1, 5),
            // <!-- Bundle Items tab -->
            'bundle_options_type_all'               => array('Radio Buttons', 'Drop-down', 'Checkbox', 'Multiple Select'),
            'bundle_shipment_type'                  => 'Separately',
            'bundle_options_title'                  => 'Radio Buttons',
            'bundle_options_type'                   => 'Radio Buttons',
            'bundle_options_required'               => 'No',
            'bundle_options_position'               => rand(1, 5),
            'bundle_items_sku'                      => array('SP-01', 'SP-02', 'SP-03'),
            'bundle_items_sku_2'                    => array('VP-01', 'VP-02', 'VP-03'),
            'bundle_items_sku_3'                    => array('DP-01', 'DP-02', 'DP-03'),
            //<!-- Associated Products tab -->
            'configurable_items_sku'                => array('SP-02', 'VP-02', 'DP-02'),
            'grouped_items_sku'                     => array('SP-01', 'VP-01', 'DP-01'),
            'valueYes'                              => 'Yes',
            'valueNo'                               => 'No'
        );*/

        //<!-- This array Data For Negativ Tests -->
        $this->Data = array(
            'type_all'                              => array(
                                        'Simple Product',
                                        'Virtual Product',
                                        'Downloadable Product',
                                        'Grouped Product',
                                        'Bundle Product',
                                        'Configurable Product',
                                        'Gift Card',
                                        ),
            //<!-- Product Settings tab -->
            'type'                                  => 'Simple Product',
            'attribute_set'                         => 'smoke_attrSet',
            'attrib_for_conf_prod'                  => 'Admin Title',
            // <!-- Genral tab -->
            'name'                                  => '',
            'description'                           => '',
            'short_description'                     => '',
            'sku'                                   => '',
            'sku_type'                              => 'Dynamic',
            'weight'                                => '',
            'weight_type'                           => 'Fixed',
            'visibility'                            => '-- Please Select --',
            // <!-- Prices tab -->
            'price'                                 => 'Text',
            'price_type'                            => 'Fixed',
            'special_price'                         => 'Text',
            'tier_price_price'                      => 'Text',
            'tier_price_qty'                        => 'Text',
            'allow_open_amount'                     => 'Yes',
            'open_amount_min'                       => 'Text',
            'open_amount_max'                       => 'Text',
            'giftcard_amounts'                      => 'Text',
            // <!-- Inventory tab -->
            'manage_stock'                          => 'Yes',
            'inventory_qty'                         => 'Text',
            // <!-- Website tab -->
            'website_name'                          => Core::getEnvConfig('backend/scope/site/name'),
            // <!-- Category tab -->
            'category_name'                         => 'st-subcat',
            // <!-- Gift Card Information tab -->
            'giftcard_type'                         => 'Combined',
            'giftcard_lifetime'                     => 'Text',
            // <!-- Downloadable Information tab -->
            'downloadable_samples_title'            => '',
            'downloadable_sample_title'             => '',
            'downloadable_sample_url'               => '',
            'downloadable_sample_sort_order'        => 'Text',
            'downloadable_links_title'              => '',
            'downloadable_link_title'               => '',
            'downloadable_link_price'               => 'Text',
            'downloadable_link_max_downloads'       => 'Text',
            'downloadable_link_sample_url'          => '',
            'downloadable_link_url'                 => '',
            'downloadable_link_sort_order'          => 'Text',
            // <!-- Bundle Items tab -->
            'bundle_shipment_type'                  => 'Separately',
            'bundle_options_title'                  => '',
            'bundle_options_position'               => 'Text',
            'bundle_items_sku'                      => array('SP-01', 'SP-02', 'SP-03'),
            //<!-- Associated Products tab -->
            'configurable_items_sku'                => array('SP-02', 'VP-02', 'DP-02'),
            'grouped_items_sku'                     => array('SP-01', 'VP-01', 'DP-01'),
            'valueYes'                              => 'Yes',
            'valueNo'                               => 'No'
        );
    }

    /**
     * Uncheck "Use Config Settings" on Inventory Tab and Gift Card Information
     * @param $path, $field, $i
     * Inventory Tab -> for fields "manage_stock" and "enable_qty_increments" set $i=2
     * and for each rest set $i=1.
     * Gift Card Information Tab -> set $i=3
     */
    public function uncheckUseDefault($path, $field, $i)
    {
        if (isset($this->Data[$field])) {
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
    public function selectProductSettings($params = array())
    {
        $result = true;
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        $this->checkAndSelectField("attribute_set", Null);
        $this->checkAndSelectField("type", Null);
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
    public function fillGeneralTab($params = array())
    {
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        //Name
        $this->checkAndFillField("name", Null);
        //Description
        $this->checkAndFillField("description", Null);
        //Short Description
        $this->checkAndFillField("short_description", Null);
        // SKU type, Weight type and Weight for Bundle Product
        if ($Data["type"] == 'Bundle Product') {
            $this->checkAndSelectField("sku_type", Null);
            $this->checkAndSelectField("weight_type", Null);
            if (isset($Data["weight_type"]) and $Data["weight_type"] == 'Fixed') {
                $this->checkAndFillField("weight", Null);
            }
        }
        //SKU
        $this->checkAndFillField("sku", Null);
        //Weight
        if ($Data["type"] == 'Simple Product' or $Data["type"] == 'Gift Card') {
            $this->checkAndFillField("weight", Null);
        }
        //Set Product as New from Date
        $this->checkAndFillField("news_from_date", Null);
        //Set Product as New to Date
        $this->checkAndFillField("news_to_date", Null);
        //Status
        $this->checkAndSelectField("status", Null);
        //URL key
        $this->checkAndFillField("url_key", Null);
        //Visibility
        $this->checkAndSelectField("visibility", Null);
        //Allow Gift Message
        $this->checkAndSelectField("allow_gift_message", Null);
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
    public function fillPricesTab($params = array())
    {
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        //Prices Tab
        $this->click($this->getUiElement("tabs/price"));
        //Price type, Price View and Price for Bundle product
        if ($Data["type"] == 'Bundle Product') {
            $this->checkAndSelectField("price_type", Null);
            $this->checkAndSelectField("price_view", Null);
            if (isset($Data["price_type"]) and $Data["price_type"] == 'Fixed') {
                $this->checkAndFillField("price", Null);
            }
        }
        //Add values on Prices Tab for Simple, Virtual, Downloadable, Configurable products
        if ($Data["type"] != 'Gift Card' and
                $Data["type"] != 'Grouped Product') {
            if ($Data["type"] != 'Bundle Product') {
                //Price
                $this->checkAndFillField("price", Null);
            }
            //Special Price
            $this->checkAndFillField("special_price", Null);
            //Special Price From Date
            $this->checkAndFillField("special_from_date", Null);
            //Special Price To Date
            $this->checkAndFillField("special_to_date", Null);
            //Tax Class
            $this->checkAndSelectField("tax_class", Null);
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
        $this->checkAndSelectField("enable_googlecheckout", Null);
        //Add Amount, Allow Open Amount, Open Amount Min Value and Open Amount Max Value for Gift Card
        if ($Data["type"] == 'Gift Card') {
            if (isset($Data["giftcard_amounts"])) {
                for ($i = 0; $i <= count($Data['giftcard_amounts']) - 1; $i++) {
                    $this->click($this->getUiElement("buttons/add_amount"));
                    $this->type($this->getUiElement("inputs/giftcard_amounts", $i), $Data["giftcard_amounts"][$i]);
                }
            }
            $this->checkAndSelectField("allow_open_amount", Null);
            $this->checkAndFillField("open_amount_max", Null);
            $this->checkAndFillField("open_amount_min", Null);
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
    public function fillInvenoryTab($params = array())
    {
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        $this->click($this->getUiElement("tabs/inventory"));
        //Manage Stock.
        $this->uncheckUseDefault("selectors/manage_stock", "manage_stock", 2);
        $this->checkAndSelectField("manage_stock", Null);
        if (isset($Data["manage_stock"]) and
                $Data["manage_stock"] == 'Yes') {
            if ($Data["type"] != 'Grouped Product' and
                    $Data["type"] != 'Configurable Product' and
                    $Data["type"] != 'Bundle Product') {
                //Qty
                $this->checkAndFillField("inventory_qty", Null);
                //Qty for Item's Status to become Out of Stock
                $this->uncheckUseDefault("inputs/inventory_min_qty", "inventory_min_qty", 1);
                $this->checkAndFillField("inventory_min_qty", Null);
                //Backorders
                $this->uncheckUseDefault("selectors/backorders", "backorders", 1);
                $this->checkAndSelectField("backorders", Null);
                //Notify for Quantity Below
                $this->uncheckUseDefault("inputs/notify_stock_qty", "notify_stock_qty", 1);
                $this->checkAndFillField("notify_stock_qty", Null);
            }
            if ($Data["type"] == 'Simple Product' or $Data["type"] == 'Virtual Product') {
                //Qty Uses Decimals
                $this->checkAndSelectField("is_qty_decimal", Null);
            }
            //Enable Qty Increments and Qty Increments
            $this->uncheckUseDefault("selectors/enable_qty_increments", "enable_qty_increments", 2);
            $this->checkAndSelectField("enable_qty_increments", Null);
            if (isset($Data["enable_qty_increments"]) and
                    isset($Data["qty_increments"]) and
                    $Data["enable_qty_increments"] == 'Yes') {
                $this->uncheckUseDefault("inputs/qty_increments", "qty_increments", 1);
                $this->checkAndFillField("qty_increments", Null);
            }
            //Stock Availability
            $this->checkAndSelectField("stock_availability", Null);
        }
        if ($Data["type"] != 'Grouped Product' and
                $Data["type"] != 'Configurable Product' and
                $Data["type"] != 'Bundle Product') {
            //Minimum Qty Allowed in Shopping Cart
            $this->uncheckUseDefault("inputs/min_sale_qty", "min_sale_qty", 1);
            $this->checkAndFillField("min_sale_qty", Null);
            //Maximum Qty Allowed in Shopping Cart
            $this->uncheckUseDefault("inputs/max_sale_qty", "max_sale_qty", 1);
            $this->checkAndFillField("max_sale_qty", Null);
        }
    }

    /**
     * mark website on Websites Tab on product page
     *
     * @param array $params May contain the following params:
     * type, website_name
     *
     */
    public function fillWebsitesTab($params = array())
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
    public function fillCategoriesTab($params = array())
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
    public function fillDownloadInfTab($params = array())
    {
        $Data = $params ? $params : $this->Data;
        $this->click($this->getUiElement("tabs/downloadable_information"));
        // Samples
        // Samples Title
        $this->checkAndFillField("downloadable_samples_title", Null);
        //Add new sample
        if (isset($Data['downloadable_sample_url']) or isset($Data['downloadable_sample_title'])) {
            $qtyDownSamples = $this->getXpathCount($this->getUiElement("elements/download_sample_container"));
            //Click Add New Row
            $this->click($this->getUiElement("buttons/add_sample_item"));
            //Title
            $this->checkAndFillField("downloadable_sample_title", $qtyDownSamples);
            //File -URL
            $this->click($this->getUiElement("inputs/downloadable_sample_url_type", $qtyDownSamples));
            $this->checkAndFillField("downloadable_sample_url", $qtyDownSamples);
            //Sort Order
            $this->checkAndFillField("downloadable_sample_sort_order", $qtyDownSamples);
        }
        // Links
        // Links Title
        $this->checkAndFillField("downloadable_links_title", Null);
        //Links can be purchased separately
        $this->checkAndSelectField("downloadable_link_purchase_type", Null);
        //add new link
        if (isset($Data['downloadable_link_title']) or isset($Data['downloadable_link_url'])) {
            $qtyDownlinks = $this->getXpathCount($this->getUiElement("elements/download_link_container"));
            //Click Add New Row
            $this->click($this->getUiElement("buttons/add_link_item"));
            //Title
            $this->checkAndFillField("downloadable_link_title", $qtyDownlinks);
            //Price
            if (isset($Data['downloadable_link_purchase_type']) and
                    $Data['downloadable_link_purchase_type'] == 'Yes') {
                $this->checkAndFillField("downloadable_link_price", $qtyDownlinks);
            }
            //Max. Downloads
            $this->checkAndFillField("downloadable_link_max_downloads", $qtyDownlinks);
            //Shareable
            $this->checkAndSelectField("downloadable_link_shareable", $qtyDownlinks);
            //Sample - URL
            $this->click($this->getUiElement("inputs/downloadable_link_sample_url_type", $qtyDownlinks));
            $this->checkAndFillField("downloadable_link_sample_url", $qtyDownlinks);
            //File - URL
            $this->click($this->getUiElement("inputs/downloadable_link_url_type", $qtyDownlinks));
            $this->checkAndFillField("downloadable_link_url", $qtyDownlinks);
            //Sort Order
            $this->checkAndFillField("downloadable_link_sort_order", $qtyDownlinks);
        }
    }

    /**
     * Filling "Associated Products" Tab on product page for Grouped and Configurable products
     *
     * @param array $params May contain the following params:
     * type, grouped_items_search_table, filter_sku,
     * grouped_items_sku, configurable_items_sku
     */
    public function fillAssociatedProductsTab($params = array())
    {
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        //Open Associated Products Tab
        $this->click($this->getUiElement("tabs/associated_products"));
        $this->pleaseWait();
        // Search and mark products
        if ($Data["type"] == 'Grouped Product') {
            $this->searchElement("grouped_items_search_table", "filter_sku", "grouped_items_sku", "mark_filtered_product", NULL);
        }
        if ($Data["type"] == 'Configurable Product') {
            $this->searchElement("configurable_items_search_table", "filter_sku", "configurable_items_sku", "mark_filtered_product", NULL);
        }
    }

    /**
     * Fill Bundle Items Tab on product page for bundle product
     *
     * @param array $params May contain the following params:
     * type,
     */
    public function fillBundleItemsTab($params = array())
    {
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        //Open Bundle Items Tab
        $this->click($this->getUiElement("tabs/bundle_items"));
        $this->pleaseWait();
        $this->checkAndSelectField("bundle_shipment_type", Null);
        //Add new bundle option(s)
        if (isset($Data['bundle_options_title'])) {
            $qtyBundleOptions = $this->getXpathCount($this->getUiElement("elements/bundle_items_container"));
            $this->click($this->getUiElement("buttons/add_new_bundle_option"));
            //Default Title
            $this->checkAndFillField("bundle_options_title", "$qtyBundleOptions");
            //Input Type
            $this->checkAndSelectField("bundle_options_type", "$qtyBundleOptions");
            //Is Required
            $this->checkAndSelectField("bundle_options_required", "$qtyBundleOptions");
            //Position
            $this->checkAndFillField("bundle_options_position", "$qtyBundleOptions");
            if (isset($Data['bundle_items_sku'])) {
                //Add product(s)
                $this->click($this->getUiElement("buttons/bundle_option_add_product", "$qtyBundleOptions"));
                $this->pleaseWait();
                $this->searchElement("bundle_items_search_table", "filter_sku", "bundle_items_sku", "mark_filtered_product", "$qtyBundleOptions");
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
    public function fillGiftCardInformTab($params = array())
    {
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        //Gift Card Information Tab
        $this->click($this->getUiElement("tabs/gift_card_information"));
        //Card Type
        $this->checkAndSelectField("giftcard_type", Null);
        //Is Redeemable
        $this->uncheckUseDefault("selectors/giftcard_is_redeemable", "giftcard_is_redeemable", 3);
        $this->checkAndSelectField("giftcard_is_redeemable", Null);
        //Lifetime (days)
        $this->uncheckUseDefault("inputs/giftcard_lifetime", "giftcard_lifetime", 3);
        $this->checkAndFillField("giftcard_lifetime", Null);
        //Allow Message
        $this->uncheckUseDefault("selectors/giftcard_allow_message", "giftcard_allow_message", 3);
        $this->checkAndSelectField("giftcard_allow_message", Null);
        //Email Template
        $this->uncheckUseDefault("selectors/giftcard_email_template", "giftcard_email_template", 3);
        $this->checkAndSelectField("giftcard_email_template", Null);
    }

    /**
     * Create product
     *
     * @param array $params May contain the following params:
     * type,
     */
    public function doCreateProduct($params = array())
    {
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/manageproducts"));
        $this->clickAndWait($this->getUiElement("buttons/addproduct"));
        if ($this->selectProductSettings()) {
            $this->fillGeneralTab();
            $this->fillPricesTab();
            $this->fillInvenoryTab();
            $this->fillWebsitesTab();
            $this->fillCategoriesTab();
            if ($Data["type"] == 'Gift Card') {
                $this->fillGiftCardInformTab();
            }
            if ($Data["type"] == 'Downloadable Product') {
                $this->fillDownloadInfTab();
            }
            if ($Data["type"] == 'Bundle Product') {
                $this->fillBundleItemsTab();
            }
            if ($Data["type"] == 'Grouped Product' or $Data["type"] == 'Configurable Product') {
                $this->fillAssociatedProductsTab();
            }
            $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
            $this->saveAndVerifyForErrors("save");
        }
    }

}