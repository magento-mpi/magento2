<?php
/**
 * Abstract test class for Admin/Product/AddSimpleProduct module
 *
 * @author Magento Inc.
 */

class Frontend_Product_Open extends Test_Frontend_Product_Abstract
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
     * Tests Category page
     *
     */

    function testProductOpen()
    {
        // Test Data
        $categoryName = Core::getEnvConfig('backend/managecategories/subcategoryname');
        $productName = Core::getEnvConfig('backend/createproduct/productname');
        $productPrice = Core::getEnvConfig('backend/createproduct/price');
        // Run test
        $this->openProduct($categoryName,$productName,$productPrice);
    }
}
?>
