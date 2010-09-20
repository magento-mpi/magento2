<?php
/**
 * Frontend_product model
 *
 * @author Magento Inc.
 */
class Model_Frontend_Product extends Model_Frontend
{
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
        $productName = $productData['name'];
        $sku = $productData['sku'];
        $categoryName = $productData['subcategoryname'];

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
