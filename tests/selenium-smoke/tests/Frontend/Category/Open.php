<?php
/**
 * Abstract test class for Admin/Product/AddSimpleProduct module
 *
 * @author Magento Inc.
 */

class Frontend_Category_Open extends Test_Frontend_Category_Abstract
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

    function testcCategoryOpen()
    {
        $this->openCategory(Core::getEnvConfig('backend/managecategories/subcategoryname'));
    }
}
?>
