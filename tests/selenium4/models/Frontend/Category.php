<?php
/**
 * Frontend_category model
 *
 * @author Magento Inc.
 */
class Model_Frontend_Category extends Model_Frontend
{
    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();

        $params = Core::getEnvConfig('backend/manage_categories');
        $params ['baseUrl'] = Core::getEnvConfig('frontend/baseUrl');
        $params ['product_name'] = Core::getEnvConfig('backend/create_product/name');

        $this->categoryData = $params;
    }

    /*
     * Open subcategory from page
     * @params $subcategoryname
     * return boolean
     */
//    public function  doOpen($params = array())
    public function  doOpen($params)
    {
        if (is_array($params)) {
            $categoryData = $params ? $params : $this->categoryData;
            $categoryName = $categoryData['categoryName'];
        } else {
            $categoryName = $params;
        };
        $this->printDebug("doOpenCategory($categoryName) started...");
        $link = '//ul[@id="nav"]';
        $nodes = explode('/', $categoryName);
        foreach ($nodes as $node) {
            $link = $link . '//li[contains(a/span,"' . $node . '")]';
        }
        $link = $link . '/a';
        $this->printDebug($link);
        //Open home page
        $this->open($this->baseUrl);

        if ($this->waitForElement($link,2)) {
            //Move to Category
            $this->clickAndWait($link);
        }
        else {
            $this->printInfo('doOpenCategory: "' . $categoryName . '" category page could not be opened');
            return false;
        }
        $this->printInfo('doOpenCategory: "' . $categoryName . '" category page has been opened');
        return true;
    }

    /**
     * Test correcteness of appearing $categoryName category page.
     * @param $subcategoryname
     * @param $product_name
     * Checks:
     *  Category can be opened
     *  Category Title
     *  existance of text-link to st-01 product
     *  existance of img-link to st-01 product
     * @param $subcategoryname
     * @return boolean
     */
    public function testCategory($params = array())
    {
        $this->printDebug('testCategory started...');
        $result = true;
        $categoryData = $params ? $params : $this->categoryData;
        $categoryName = $categoryData['subcategoryname'];
        $productName =  $categoryData['product_name'];
        
        // Check for presence of Category Title
        if (!$this->waitForElement($this->getUiElement("frontend/pages/category/elements/categoryTitle",$categoryName),2)) {
            $this->setVerificationErrors('Check 1: No "Category Title = "' . $categoryName . '" founded');
            $result = false;
        }

        // Check for existance of st-01 productName  text-link
        if (!$this->waitForElement($this->getUiElement("frontend/pages/category/links/productName",$productName),2)) {
            $this->setVerificationErrors('Check 2: No "ProductName Link" founded');
            $result = false;
        }
        // Check for existance of st-01 product imgage title
        $href=strtolower(preg_replace('/( )|(\.)/', '-', $productName));
        $paramArray = array (
            '1' => $productName,
            '2' => $href
        );
        if (!$this->waitForElement($this->getUiElement("frontend/pages/category/links/productImg",$paramArray),2)) {
            $this->setVerificationErrors('Check 3: No "ProductName Image Link" founded');
            $result = false;
        }
        if ($result) {
            $this->printDebug('testCategory finished');
        }
        return $result;
    }

}
