<?php
/**
 * Abstract test class for Admin/Product/AddSimpleProduct module
 *
 * @author Magento Inc.
 */

class Admin_Category_Add extends Test_Admin_Product_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();

        // Get test parameters
        // skipped ...
        //  will be loaded directly from config.xml
    }

    /**
     * Test addion new sub category to the $StoreView store view
     *
     *@param sku
     *@param productName
     *@param categoryName
     *@param webSiteName
     *@param storeViewName
     */

    function testSimpleProductCreation() {
        Core::debug("testSimpleProductCreation started");
        // Test Dara
        $sku = Core::getEnvConfig('backend/createproduct/sku');
        $productName = Core::getEnvConfig('backend/createproduct/productname');
        $price = Core::getEnvConfig('backend/createproduct/price');
        $categoryName = Core::getEnvConfig('backend/managecategories/subcategoryname');
        $webSiteName = Core::getEnvConfig('backend/managestores/site/name');
        $storeViewName = Core::getEnvConfig('backend/managecategories/storeview');
        // Test Flow
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->addSimpleProduct($sku, $productName, $categoryName, $webSiteName, $storeViewName, $price);
        Core::debug("testSimpleProductCreation finished");
    }
}
?>
