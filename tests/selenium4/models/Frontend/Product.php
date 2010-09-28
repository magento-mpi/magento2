<?php
/**
 * Frontend_product model
 *
 * @author Magento Inc.
 */
class Model_Frontend_Product extends Model_Frontend
{

    /**
     * Product type constants
     */
    const SIMPLE   = 1;
    const GROUPED = 2;
    const CONFIGURABLE  = 3;
    const BUNDLE = 4;
    const VIRTUAL = 5;
    const GIFTCARD = 6;
    const DOWNLODABLE = 7;

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $params = Core::getEnvConfig('backend/create_product');
        $params ['subcategoryname'] = Core::getEnvConfig('backend/manage_categories/subcategoryname');

        $this->productData = $params;

        $this->categoryModel = $this->getModel('frontend/category');
    }

    /*
     * Open product page from category page
     * @params array
     * return boolean
     */
    public function  doOpen($params = array())
    {
        $this->printDebug('doOpenProduct started...');
        $productData = $params ? $params : $this->productData;
//        print_r($productData);
        $productName = $productData['productName'];
//        $sku = $productData['sku'];
//        $categoryName = $productData['subcategoryname'];

        if ($this->categoryModel->doOpen ($productData)) {
            if ($this->waitForElement($this->getUiElement("/frontend/pages/category/links/productName",$productName),5)) {
                //Move to ProductPage
                $this->clickAndWait($this->getUiElement("/frontend/pages/category/links/productName",$productName));
            } else {
                    $this->printInfo('doOpenProduct: "' . $productName . '" product page could not be opened');
                    return false;
            }
                $this->printInfo('doOpenProduct: "' . $productName . '" product page has been opened');
            return true;
        }

     }

    /*
     * Tries to determine type of opened product.
     * Default value is SIMPLE(VIRTUAL)
     */
    public function detectType()
    {
        $this->printDebug('detectType() started');
        $result = self::SIMPLE;

        $type_markers = $this->getUiElement('frontend/pages/product/elements/types');

        if ($this->isElementPresent($type_markers['grouped'])) {
            $result = self::GROUPED;
        }   elseif ($this->isElementPresent($type_markers['downlodable'])) {
                $result = self::DOWNLODABLE;
            }   elseif ($this->isElementPresent($type_markers['configurable'])) {
                    $result = self::CONFIGURABLE;
                }   elseif ($this->isElementPresent($type_markers['bundle'])) {
                        $result = self::BUNDLE;
                    }

        $this->printDebug('detectType() finished: ' . $result);
        return $result;
    }

    /*
     *
     */
    public function placeToCart($params = array())
    {
        $this->printDebug('placeToCart() started...');
        switch ($this->detectType()) {
            case self::SIMPLE:
                // Place product to the cart
                $this->type($this->getUiElement("/frontend/pages/product/inputs/qty"),$params["qty"]);
                $this->clickAndWait($this->getUiElement("/frontend/pages/product/buttons/addToCart"));
                break;
        }
        $this->printDebug('placeToCart() finished');
    }

    // Test case

    /**
     * Test correcteness of appearing $product category page.
     * Checks:
     *  1 - Product Image element
     *  2 - productName on breadcrumbs
     *  3 - productName on product-name section
     *  4 - PriceOnPage matched to config value
     *  5 - product page could be opened
     * @param name - product Name
     * @param subcategoryname - category name
     * @return boolean
     */
    public function testProduct($params = array())
    {
        $productData = $params ? $params : $this->productData;
        $categoryName = $productData['subcategoryname'];
        $productName =  $productData['name'];
        $price = $productData['price'];
        $this->printDebug("testProduct($productName) started...");

        $result = true;
        if ($this->doOpen($productData)) {
            // Do some checks:
            // Check for presence of Product Image
            if (!$this->waitForElement($this->getUiElement("frontend/pages/product/elements/productImage"),2)) {
                $this->setVerificationErrors('Check 1: No Product Image on page has been founded');
                $result = false;
            }

            // Check for presence product name on breadcrumbs
            if (!$this->waitForElement($this->getUiElement("frontend/pages/product/elements/breadcrumb",$productName),2)) {
                $this->setVerificationErrors('Check 2: No productName on breadcrumbs has been founded');
                $result = false;
            }

            // Check for presence product name on product-page
            if (!$this->waitForElement($this->getUiElement("frontend/pages/product/elements/name",$productName),2)) {
                $this->setVerificationErrors('Check 3: No productName on product-name section has been founded');
                $result = false;
            }

            // Check for presence correct price on product-page
            if ($this->waitForElement($this->getUiElement("frontend/pages/product/elements/price"),2)) {
                $priceOnPage = $this->getText($this->getUiElement("frontend/pages/product/elements/price"));
                $priceFromConfig = $this->money_format('%.2n',$price);
                if ($priceOnPage != $priceFromConfig) {
                    $this->setVerificationErrors('Check 4: PriceOnPage [' . $priceOnPage . '] did not matched to expected [' . $priceFromConfig . ']');
                    $result = false;
                }
            } else {
                $this->setVerificationErrors('Check 5: No price-box has been founded');
                $result = false;
            }

        } else {
            $this->setVerificationErrors("Check 6: Product $productName  page could not be opened");
            $result = false;
        }
        return $result;
    }

}
