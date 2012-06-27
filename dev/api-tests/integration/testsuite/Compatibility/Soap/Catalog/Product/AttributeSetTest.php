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
class Compatibility_Soap_Catalog_Product_AttributeSetTest extends Compatibility_Soap_SoapAbstract
{

    /**
     * Test product attribute set current store method compatibility.
     * Scenario:
     * 1. Get product attribute set list at previous API.
     * 2. Get product attribute set list at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     */
    public function testProductAttributeSetList()
    {
        $apiMethod = 'product_attribute_set.list';
        $prevResponse = $this->prevCall($apiMethod);
        $currResponse = $this->currCall($apiMethod);
        $this->_checkResponse($prevResponse, $currResponse, $apiMethod);
        $this->_checkVersionSignature($prevResponse[0], $currResponse[0], $apiMethod);
    }

    /**
     * Test product attribute set create current store method compatibility.
     * Scenario:
     * 1. Get product attribute set create at previous API.
     * 2. Get product attribute create list at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     */
    public function testProductAttributeSetCreate()
    {
        $apiMethod = 'product_attribute_set.create';
        $entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');
        $attributeSetName = 'Test Attribute Set Name';
        $attributeSetSkeletonId = $entityType->getDefaultAttributeSetId();
        $prevResponse = $this->prevCall($apiMethod, array($attributeSetName, $attributeSetSkeletonId));
        $currResponse = $this->currCall($apiMethod, array($attributeSetName, $attributeSetSkeletonId));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

}
