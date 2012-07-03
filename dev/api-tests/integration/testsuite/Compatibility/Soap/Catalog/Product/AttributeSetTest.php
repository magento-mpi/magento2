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
     * Attribute Set ID created at previous API
     * @var int
     */
    protected static $_prevAttributeSetId;

    /**
     * Attribute Set ID created at current API
     * @var int
     */
    protected static $_currAttributeSetId;

    /**
     * Product Attribute ID created at previous API
     * @var int
     */
    protected static $_prevProductAttributeId;

    /**
     * Product Attribute ID created at current API
     * @var int
     */
    protected static $_currProductAttributeId;

    /**
     * Attribute Set Group ID created at previous API
     * @var int
     */
    protected static $_prevAttributeSetGroupId;

    /**
     * Attribute Set Group ID created at current API
     * @var int
     */
    protected static $_currAttributeSetGroupId;

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
        $attributeSetSkeletonId = $entityType->getDefaultAttributeSetId();
        $attributeSetName = 'Test Attribute Set Name' . uniqid();
        self::$_prevAttributeSetId = $this->prevCall($apiMethod, array($attributeSetName, $attributeSetSkeletonId));
        self::$_currAttributeSetId = $this->currCall($apiMethod, array($attributeSetName, $attributeSetSkeletonId));
        $this->_checkVersionType(self::$_prevAttributeSetId, self::$_currAttributeSetId, $apiMethod);
    }

    /**
     * Test product attribute set create current store method compatibility.
     * Scenario:
     * 1. Get product attribute set create at previous API.
     * 2. Get product attribute create list at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testProductAttributeSetCreate
     */
    public function testProductAttributeSetGroupAdd()
    {
        $apiMethod = 'product_attribute_set.groupAdd';
        $groupName = 'Test Group Name' . uniqid();
        self::$_prevAttributeSetGroupId = $this->prevCall($apiMethod, array(self::$_prevAttributeSetId, $groupName));
        self::$_currAttributeSetGroupId = $this->currCall($apiMethod, array(self::$_currAttributeSetId, $groupName));
        $this->_checkVersionType(self::$_prevAttributeSetGroupId, self::$_currAttributeSetGroupId, $apiMethod);
    }

    /**
     * Test product attribute set create current store method compatibility.
     * Scenario:
     * 1. Get product attribute set create at previous API.
     * 2. Get product attribute create list at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testProductAttributeSetGroupAdd
     */
    public function testProductAttributeSetGroupRename()
    {
        $apiMethod = 'product_attribute_set.groupRename';
        $newGroupName = 'New Test Group Name' . uniqid();
        $prevResponse = $this->prevCall($apiMethod, array(self::$_prevAttributeSetGroupId, $newGroupName));
        $currResponse = $this->currCall($apiMethod, array(self::$_currAttributeSetGroupId, $newGroupName));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test product attribute set create current store method compatibility.
     * Scenario:
     * 1. Get product attribute set create at previous API.
     * 2. Get product attribute create list at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testProductAttributeSetGroupAdd
     */
    public function testProductAttributeSetGroupRemove()
    {
        $apiMethod = 'product_attribute_set.groupRemove';
        $prevResponse = $this->prevCall($apiMethod, array(self::$_prevAttributeSetGroupId));
        $currResponse = $this->currCall($apiMethod, array(self::$_currAttributeSetGroupId));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
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
    public function testProductAttributeSetAttributeAdd()
    {
        $apiMethod = 'product_attribute_set.attributeAdd';
        $productAttributeIds = $this->_createProductAttributes();
        self::$_prevProductAttributeId = $productAttributeIds['prevProductAttributeId'];
        self::$_currProductAttributeId = $productAttributeIds['currProductAttributeId'];
        $prevResponse = $this->prevCall($apiMethod, array(self::$_prevProductAttributeId, self::$_prevAttributeSetId));
        $currResponse = $this->currCall($apiMethod, array(self::$_currProductAttributeId, self::$_currAttributeSetId));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test product attribute set create current store method compatibility.
     * Scenario:
     * 1. Get product attribute set create at previous API.
     * 2. Get product attribute create list at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testProductAttributeSetAttributeAdd
     */
    public function testProductAttributeSetAttributeRemove()
    {
        $apiMethod = 'product_attribute_set.attributeRemove';
        $prevResponse = $this->prevCall($apiMethod, array(self::$_prevProductAttributeId, self::$_prevAttributeSetId));
        $currResponse = $this->currCall($apiMethod, array(self::$_currProductAttributeId, self::$_currAttributeSetId));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
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
    public function testProductAttributeSetRemove()
    {
        $apiMethod = 'product_attribute_set.remove';
        $prevResponse = $this->prevCall($apiMethod, self::$_prevAttributeSetId);
        $currResponse = $this->currCall($apiMethod, self::$_currAttributeSetId);
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

}
