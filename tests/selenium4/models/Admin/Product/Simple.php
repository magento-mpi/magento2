<?php

/**
 * Admin_Product_Simple model
 *
 * @author Magento Inc.
 */
class Model_Admin_Product_Simple extends Model_Admin {

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData() {
        parent::loadConfigData();

        $this->productData = array(
            'sku' => Core::getEnvConfig('backend/createproduct/sku'),
            'productName' => Core::getEnvConfig('backend/createproduct/productname'),
            'price' => Core::getEnvConfig('backend/createproduct/price'),
            'categoryName' => Core::getEnvConfig('backend/managecategories/subcategoryname'),
            'webSiteName' => Core::getEnvConfig('backend/scope/site/name'),
            'storeViewName' => Core::getEnvConfig('backend/scope/store_view/name'),
            'description' => 'description',
            'short_description' => 'short description',
            'weight' => '10',
            'quantity' => '1000',
            'duplicatedSku' => Core::getEnvConfig('backend/createproduct/duplicatedsku'),
        );
        $this->skuData = array(
            1 => Core::getEnvConfig('backend/createproduct/sku'),
            2 => Core::getEnvConfig('backend/createproduct/duplicatedsku'),
        );
    }

    /**
     * Adds new simple product with next patrameters:
     * @param sku
     * @param productName
     * @param categoryName
     * @param webSiteName
     * @param storeViewName
     * @param price
     *
     */
    public function doAddSimpleProduct($params = array()) {
        $productData = $params ? $params : $this->productData;

        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts');

        // Open Manage Products Page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/manageproducts"));
        $this->clickAndWait($this->getUiElement("buttons/addproduct"));
        $this->clickAndWait($this->getUiElement("buttons/addproductcontinue"));
        //Fill Product Page
        //General Tab
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
        $this->type($this->getUiElement("inputs/name"), $productData["productName"]);
        $this->type($this->getUiElement("inputs/description"), $productData["description"]);
        $this->type($this->getUiElement("inputs/short_description"), $productData["short_description"]);
        $this->type($this->getUiElement("inputs/sku"), $productData["sku"]);
        $this->type($this->getUiElement("inputs/weight"), $productData["weight"]);
        $this->select($this->getUiElement("selectors/status"), "label=Enabled");
        // Price Tab
        $this->click($this->getUiElement("tabs/price"));
        $this->type($this->getUiElement("inputs/price"), $productData["price"]);
        $this->select($this->getUiElement("selectors/tax_class"), "label=None");
        // Inventory Tab
        $this->click($this->getUiElement("tabs/inventory"));
        $this->type($this->getUiElement("inputs/inventory_qty"), $productData["quantity"]);
        $this->select($this->getUiElement("selectors/inventory_stock_availability"), "label=In Stock");
        // Websites Tab
        $this->click($this->getUiElement("tabs/websites"));
        $this->click($this->getUiElement("inputs/website", $productData["webSiteName"]));
        // Categories Tab
        $this->click($this->getUiElement("tabs/categories"));
        $this->model->pleaseWait();
        $this->click($this->getUiElement("inputs/category", $productData["categoryName"]));
        //Save product
        $this->click($this->getUiElement("buttons/save"));
        // wait for any message
        if ($this->waitForElement($this->getUiElement("/admin/messages/message"), 30)) {
            //check for error message
            if ($this->waitForElement($this->getUiElement("/admin/messages/error"), 1)) {
                $etext = $this->getText($this->getUiElement("/admin/messages/error"));
                $this->setVerificationErrors("Check 1: " . $etext);
            }
        } else {
            // Check for success message
            if (!$this->waitForElement($this->getUiElement("/admin/messages/success"), 1)) {
                $this->setVerificationErrors("Check 2: no success message");
            }
            //Check for some specific validation errors:
            // sku must be unique
            if ($this->isElementPresent($this->getUiElement("messages/skumustbeunique"), 2)) {
                $this->setVerificationErrors("Check 3: SKU must be unique");
            }
        }
    }

    /**
     * Open product for the editing
     * @param $sku 
     * @returns boolean
     */
    public function doOpenProduct($params = array()) {
        $productData = $params ? $params : $this->productData;
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/manageproducts"));
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts');
        $this->click($this->getUiElement("buttons/reset_filter"));
        $this->model->pleaseWait();
        $this->type($this->getUiElement("inputs/filter_sku"), $productData["sku"]);
        $this->click($this->getUiElement("buttons/search"));
        $this->model->pleaseWait();

        if ($this->waitForElement($this->getUiElement('elements/filtered_product', $productData["sku"]), 20)) {
            $this->clickAndWait($this->getUiElement('elements/filtered_product', $productData["sku"]));
            return true;
        } else {
            $this->printInfo("Product with sku=" . $productData["sku"] . " could not be loaded");
            return false;
        }
    }

    /**
     * Duplicate product with specified @sku
     * @param $sku - original product Sku
     * @param $duplicatedSku - duplicated product Sku
     *
     */
    public function duplicateProduct($params = array()) {
        $productData = $params ? $params : $this->productData;
        if ($this->model->doOpenProduct()) {
            $result = true;
            //Open source product
            //if ($this->model->doOpenProduct()) {
            $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
            //if such product exists ...
            //Duplicate
            $this->clickAndWait($this->getUiElement("buttons/duplicate"));
            // Check for success message
            if (!$this->waitForElement($this->getUiElement("messages/product_duplicated"), 40)) {
                $this->setVerificationErrors("Check 1: no success duplicated message");
                $result = false;
            }
            //Change SKU
            $this->type($this->getUiElement("inputs/sku"), $productData["duplicatedSku"]);
            //Save product
            $this->click($this->getUiElement("buttons/save"));
            // wait for any message
            if ($this->waitForElement($this->getUiElement("/admin/messages/message"), 20)) {
                //check for error message
                if ($this->waitForElement($this->getUiElement("/admin/messages/error"), 1)) {
                    $etext = $this->getText($this->getUiElement("/admin/messages/error"));
                    $this->setVerificationErrors("Check 1: " . $etext);
                    $result = false;
                }
            } else {
                // Check for success message
                if (!$this->waitForElement($this->getUiElement("/admin/messages/success"), 1)) {
                    $this->setVerificationErrors("Check 2: no success message");
                    $result = false;
                }
                //Check for some specific validation errors:
                // sku must be unique
                if ($this->isElementPresent($this->getUiElement("messages/skumustbeunique"), 2)) {
                    $this->setVerificationErrors("Check 3: SKU must be unique");
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * Delete product
     * @param $sku
     */
    public function doDeleteProduct($params = array()) {
        $productData = $params ? $params : $this->productData;
        if ($this->model->doOpenProduct($params)) {
            $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
            $this->chooseOkOnNextConfirmation();
            $this->clickAndWait($this->getUiElement("buttons/delete"));
            $this->waitForElement($this->getUiElement("/admin/messages/success"), 10);
            if (!$this->isElementPresent($this->getUiElement("/admin/messages/success"))) {
                $this->setVerificationErrors("No success message about deleting");
                $result = false;
            }
        }
    }

    /**
     * Delete Multiple products using product sku's
     * @param 
     */
    public function doDeleteMultipleProducts($params = array()) {
        $skuData = $params ? $params : $this->skuData;
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/manageproducts"));
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts');
        $this->click($this->getUiElement("buttons/reset_filter"));
        $this->model->pleaseWait();
        $i = 1;
        while ($i <= 2) {
            $this->type($this->getUiElement("inputs/filter_sku"), $skuData[$i]);
            $this->click($this->getUiElement("buttons/search"));
            $this->model->pleaseWait();
            if ($this->waitForElement($this->getUiElement('elements/filtered_product', $skuData[$i]), 20)) {
                $this->click($this->getUiElement('inputs/select_product', $skuData[$i]));
            } else {
                $this->setVerificationErrors("Product with sku=" . $skuData[$i] . " does not exist");
                return false;
            }
            $i++;
        }
        $this->select($this->getUiElement("inputs/select_actions"), "label=Delete");
        $this->chooseOkOnNextConfirmation();
        $this->clickAndWait($this->getUiElement("buttons/sumbit_actions"));
        if (!$this->isElementPresent($this->getUiElement("/admin/messages/success"))) {
            $this->setVerificationErrors("No success message about deleting");
            $result = false;
        }
    }

}