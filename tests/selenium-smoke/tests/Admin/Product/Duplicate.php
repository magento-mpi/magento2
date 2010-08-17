<?php
/**
 * Abstract test class for Admin/Product/AddSimpleProduct module
 *
 * @author Magento Inc.
 */

class Admin_Product_Duplicate extends Test_Admin_Product_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp()
    {
        parent::setUp();
    }

    /**
     * Test product duplication process
     *
     * Test Data:
     * @sku
     * @duplicatedSku
     */

    function testDuplicate()
    {
        Core::debug("testProductDuplicate started");
        // Test Dara
        $sku = Core::getEnvConfig('backend/createproduct/sku');
        $duplicatedSku = Core::getEnvConfig('backend/createproduct/duplicatedSku');
            
        // Test Flow
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->duplicate($sku,$duplicatedSku);
        Core::debug("testProductDuplicate finished");
    }
}
?>
