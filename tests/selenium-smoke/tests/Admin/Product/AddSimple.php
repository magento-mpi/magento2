<?php
/**
 * Abstract test class for Admin/Product/AddSimpleProduct module
 *
 * @author Magento Inc.
 */

class Admin_Product_AddSimple extends Test_Admin_Product_Abstract
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
        // Test Dara
        $paramArray = array (
            "sku" => Core::getEnvConfig('backend/createproduct/sku'),
            "productName" =>  Core::getEnvConfig('backend/createproduct/productname'),
            "description" =>  Core::getEnvConfig('backend/createproduct/productname')." description",
            "short_description" =>  Core::getEnvConfig('backend/createproduct/productname')." short description",
            "weight" => "10",
            "price" => Core::getEnvConfig('backend/createproduct/price'),
            "quantity" => "1000",
            "categoryName" => Core::getEnvConfig('backend/managecategories/subcategoryname'),
            "webSiteName" => Core::getEnvConfig('backend/managestores/site/name')
        );       
        // Test Flow
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->addSimpleProduct($paramArray);
    }
}
?>
