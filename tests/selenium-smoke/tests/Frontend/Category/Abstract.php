<?php
/**
 * Abstract test class for Frontend module
 *
 * @author Magento Inc.
 */
abstract class Test_Frontend_Category_Abstract extends Test_Frontend_Abstract
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
     * Ttest correcteness of appearing $categoryName category page.
     * Checks:
     *  Category can be opened
     *  Category Title
     *  existance of text-link to st-01 product
     *  existance of img-link to st-01 product
     * @param $categoryName
     * @return boolean
     */
    public function openCategory($categoryName)
    {
        $result = true;
        //Open home page
        $this->open($this->_baseurl);
        
        if ($this->doOpenCategory($categoryName)) {
            // Do some checks:
            $productName = Core::getEnvConfig('backend/createproduct/productname');
            // Check for presence of Category Title
            if (!$this->waitForElement($this->getUiElement("frontend/pages/category/elements/categoryTitle",$categoryName),2)) {
                $this->setVerificationErrors('Check 1: No "Category Title = "' . $categoryName . '" founded');
                $result = false;
            }

            // Check for existance of st-01 productName  text-link
            $productName = Core::getEnvConfig('backend/createproduct/productname');
            if (!$this->waitForElement($this->getUiElement("frontend/pages/category/links/productName",$productName),2)) {
                $this->setVerificationErrors('Check 2: No "ProductName Link" founded');
                $result = false;
            }
            // Check for existance of st-01 product imgage title
            $paramArray = array (
                '1' => $productName,
                '2' => $productName
            );
            if (!$this->waitForElement($this->getUiElement("frontend/pages/category/links/productImg",$paramArray),2)) {
                $this->setVerificationErrors('Check 3: No "ProductName Image Link" founded');
                $result = false;
            }
        } else {
            $this->setVerificationErrors('Check 4: Category "' . $categoryName . '" page could not be opened');
            $result = false;
        }
        return $result;
    }
}

