<?php
/**
 * Set of tests of layout directives handling behavior
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_DataService_LayoutTest_Magento_Catalog_Service_TestProduct
{
    /**
     * Provide test product data fixture
     */
    public function getTestProduct($someArgName)
    {
        return array(
            'testProduct' => array(
                'id' => $someArgName
            )
        );
    }
}