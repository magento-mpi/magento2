<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  compatibility_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Product methods compatibility between previous and current API versions.
 */
class Compatibility_Soap_ProductTypeTest extends Compatibility_Soap_SoapAbstract
{
    /**
     * Test product type list method compatibility.
     * Scenario:
     * 1. Get product type list at previous API.
     * 2. Get product type list at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     */
    public function testProductTypeList()
    {
        $apiMethod = 'product_type.list';
        $prevResponse = $this->prevCall($apiMethod, array('filters' => ''));
        $currResponse = $this->currCall($apiMethod, array('filters' => ''));
        $this->_checkResponse($prevResponse, $currResponse, $apiMethod);
        $this->_checkVersionSignature($prevResponse, $currResponse, $apiMethod);
    }
}
