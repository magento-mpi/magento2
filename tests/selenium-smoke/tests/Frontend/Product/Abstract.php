<?php
/**
 * Abstract test class for Frontend module
 *
 * @author Magento Inc.
 */
abstract class Test_Frontend_Product_Abstract extends Test_Frontend_Abstract
{
    /**
     * Helper local instance
     *
     * @var Helper_Admin
     */
    protected $_helper = null;

    /**
     * Initialize the environment
     */
    public function  setUp()
    {
        parent::setUp();

        // Get test parameters
        $this->_baseurl = Core::getEnvConfig('frontend/baseUrl');
    }

    /**
     * Cpen $categoryName category page, find $productName link and open $productName page
     * @param $categoryName
     * @param $productName
     * @return boolean
     */
    public function doOpenProduct($categoryName, $productName)
    {
        if ($this->doOpenCategory($categoryName)) {
            if ($this->waitForElement($this->getUiElement("frontend/pages/category/links/productName",$productName),5)) {
                //Move to Category
                $this->clickAndWait($this->getUiElement("frontend/pages/category/links/productName",$productName));
            } else {
                    Core::debug('doOpenProduct: "' . $productName . '" product page could not be opened', 5);
                    return false;
            }
            Core::debug('doOpenProduct: "' . $productName . '" product page has been opened', 7);
            return true;
        }
    }

    /**
     * Test correcteness of appearing $categoryName category page.
     * Checks:
     *  Category can be opened
     *  Category Title
     *  existance of text-link to st-01 product
     *  existance of img-link to st-01 product
     * @param $categoryName
     * @return boolean
     */
    public function openProduct($categoryName, $productName, $price)
    {
        $result = true;
        //Open home page
        $this->open($this->_baseurl);

        if ($this->doOpenProduct($categoryName, $productName)) {
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
            $this->setVerificationErrors('Check 4: Category "' . $categoryName . '" page could not be opened');
            $result = false;
        }
        return $result;
    }
}

