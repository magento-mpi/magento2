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
            'sku' => Core::getEnvConfig('backend/create_product/sku'),
            'productName' => Core::getEnvConfig('backend/create_product/name'),
            'price' => Core::getEnvConfig('backend/create_product/price'),
            'categoryName' => Core::getEnvConfig('backend/manage_categories/subcategoryname'),
            'webSiteName' => Core::getEnvConfig('backend/scope/site/name'),
            'storeViewName' => Core::getEnvConfig('backend/scope/store_view/name'),
            'description' => 'description',
            'short_description' => 'short description',
            'weight' => '10',
            'quantity' => '1000',
            'duplicatedSku' => Core::getEnvConfig('backend/create_product/duplicated_sku'),
        );
        $this->skuData = array(
            1 => Core::getEnvConfig('backend/create_product/sku'),
            2 => Core::getEnvConfig('backend/create_product/duplicated_sku'),
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
        $this->printDebug('doAddSimpleProduct() started...');
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
        $this->select($this->getUiElement("selectors/stock_availability"), "label=In Stock");
        // Websites Tab
        $this->click($this->getUiElement("tabs/websites"));
        $this->click($this->getUiElement("inputs/website", $productData["webSiteName"]));
        // Categories Tab
        $this->click($this->getUiElement("tabs/categories"));
        $this->pleaseWait();
        $this->click($this->getUiElement("inputs/category", $productData["categoryName"]));
        //Save product
        $this->click($this->getUiElement("buttons/save"));

        // wait for any message
        if ($this->waitForElement($this->getUiElement("/admin/messages/message"), 60)) {
            //check for error message
            if ($this->waitForElement($this->getUiElement("/admin/messages/error"), 2)) {
                $etext = $this->getText($this->getUiElement("/admin/messages/error"));
                $this->setVerificationErrors("Check 1: " . $etext);
            } else {
                // Check for success message
                if (!$this->waitForElement($this->getUiElement("/admin/messages/success"), 20)) {
                    $this->setVerificationErrors("Check 2: no success message");
                    //Check for some specific validation errors:
                    // sku must be unique
                    if ($this->isElementPresent($this->getUiElement("messages/skumustbeunique"), 2)) {
                        $this->setVerificationErrors("Check 3: SKU must be unique");
                    }
                } else {
                    $this->printInfo('Product ' . $productData["productName"] . ' has been created');
                }
            }
        } else {
            $this->setVerificationErrors("Check 4: No any messages from Magento. Hangs up ?");
        }
        $this->printDebug('doAddSimpleProduct() finished');
//        sleep(60);
    }

    /**
     * Open product for the editing
     * @param $sku 
     * @returns boolean
     */
    public function doOpenProduct($params = array())
    {
        $productData = $params ? $params : $this->productData;
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/manageproducts"));
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts');
        $this->click($this->getUiElement("buttons/reset_filter"));
        $this->pleaseWait();
        $this->type($this->getUiElement("inputs/filter_sku"), $productData["sku"]);
        $this->click($this->getUiElement("buttons/search"));
        $this->pleaseWait();

        if ($this->isTextPresent($this->getUiElement('/admin/elements/no_records'),2)) {
            $this->printInfo("Product with sku=" . $productData["sku"] . " could not be opened");
            return false;
        } else {
            if ($this->waitForElement($this->getUiElement('elements/filtered_product', $productData["sku"]), 20)) {
                $this->clickAndWait($this->getUiElement('elements/filtered_product', $productData["sku"]));
                $this->printInfo("Product with sku=" . $productData["sku"] . " opened");
                return true;
                }
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
        $result = true;
        if ($this->doOpenProduct()) {            
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
            //Change Name
            $this->type($this->getUiElement("inputs/name"), $productData["productName"]);
            //Save product
            $this->click($this->getUiElement("buttons/save"));

            // wait for any message
            if ($this->waitForElement($this->getUiElement("/admin/messages/message"), 30)) {
                //check for error message
                if ($this->waitForElement($this->getUiElement("/admin/messages/error"), 2)) {
                    $etext = $this->getText($this->getUiElement("/admin/messages/error"));
                    $this->setVerificationErrors("Check 1: " . $etext);
                } else {
                    // Check for success message
                    if (!$this->waitForElement($this->getUiElement("/admin/messages/success"), 20)) {
                        $this->setVerificationErrors("Check 2: no success message");
                        //Check for some specific validation errors:
                        // sku must be unique
                        if ($this->isElementPresent($this->getUiElement("messages/skumustbeunique"), 2)) {
                            $this->setVerificationErrors("Check 3: SKU must be unique");
                        }
                    } else {
                        $this->printInfo('Product ' . $productData["productName"] . ' has been created');
                    }
                }
            } else {
                $this->setVerificationErrors("Check 4: No any messages from Magento. Hangs up ?");
            }
        }
        if ($result) {
            $this->printInfo('Product sku=' . $productData["duplicatedSku"] . ' has been duplicated');
        }
        return $result;
    }

    /**
     * Delete product
     * @param $sku
     */
    public function doDeleteProduct($params = array()) {
        $productData = $params ? $params : $this->productData;
        $result = false;
        $this->printDebug('doDeleteProduct() started...');        
        if ($this->doOpenProduct($params)) {
            $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
            $this->clickAndWait($this->getUiElement("buttons/delete"));
            $this->assertConfirmation('Are you sure?');
            $this->chooseOkOnNextConfirmation();
            $this->waitForElement($this->getUiElement("/admin/messages/success"), 10);
            if (!$this->isElementPresent($this->getUiElement("/admin/messages/success"))) {
                $this->setVerificationErrors("doDeleteProduct: No success message");
                $result = false;
            } else {
                $this->printInfo('Product ' . $productData['sku'] . ' has been deleted');
                $result = true;
            }
        }
        $this->printDebug('doDeleteProduct() finished');
        return $result;
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
        $this->pleaseWait();
        $i = 1;
        while ($i <= 2) {
            $this->type($this->getUiElement("inputs/filter_sku"), $skuData[$i]);
            $this->click($this->getUiElement("buttons/search"));
            $this->pleaseWait();
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