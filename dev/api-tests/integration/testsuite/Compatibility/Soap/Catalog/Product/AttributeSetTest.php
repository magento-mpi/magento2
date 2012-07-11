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
     * Test product attribute set list method compatibility.
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
     * Test product attribute set create method compatibility.
     * Scenario:
     * 1. Create product attribute set at previous API.
     * 2. Create product attribute set at current API.
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
        self::$_prevAttributeSetId = $this->prevCall($apiMethod, array(
            'attributeSetName' => $attributeSetName,
            'skeletonSetId' => $attributeSetSkeletonId
        ));
        self::$_currAttributeSetId = $this->currCall($apiMethod, array('attributeSetName' => $attributeSetName, 'skeletonSetId' => $attributeSetSkeletonId));
        $this->_checkVersionType(self::$_prevAttributeSetId, self::$_currAttributeSetId, $apiMethod);
    }

    /**
     * Test product attribute set group add method compatibility.
     * Scenario:
     * 1. Add group to the attribute set, created in 'testProductAttributeSetCreate' method, at previous API.
     * 2. Add group to the attribute set, created in 'testProductAttributeSetCreate' method, at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testProductAttributeSetCreate
     */
    public function testProductAttributeSetGroupAdd()
    {
        $apiMethod = 'product_attribute_set.groupAdd';
        $groupName = 'Test Group Name' . uniqid();
        self::$_prevAttributeSetGroupId = $this->prevCall($apiMethod, array(
            'attributeSetId' => self::$_prevAttributeSetId,
            'groupName' => $groupName
        ));
        self::$_currAttributeSetGroupId = $this->currCall($apiMethod, array(
            'attributeSetId' => self::$_currAttributeSetId,
            'groupName' => $groupName
        ));
        $this->_checkVersionType(self::$_prevAttributeSetGroupId, self::$_currAttributeSetGroupId, $apiMethod);
    }

    /**
     * Test product attribute set group rename method compatibility.
     * Scenario:
     * 1. Rename group, added in 'testProductAttributeSetGroupAdd' method, at previous API.
     * 2. Rename group, added in 'testProductAttributeSetGroupAdd' method, at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testProductAttributeSetGroupAdd
     */
    public function testProductAttributeSetGroupRename()
    {
        $apiMethod = 'product_attribute_set.groupRename';
        $newGroupName = 'New Test Group Name' . uniqid();
        $prevResponse = $this->prevCall($apiMethod, array('groupId' => self::$_prevAttributeSetGroupId, 'groupName' => $newGroupName));
        $currResponse = $this->currCall($apiMethod, array('groupId' => self::$_currAttributeSetGroupId, 'groupName' => $newGroupName));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test product attribute set group remove method compatibility.
     * Scenario:
     * 1. Remove group, added in 'testProductAttributeSetGroupAdd' method, at previous API.
     * 2. Remove group, added in 'testProductAttributeSetGroupAdd' method, at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testProductAttributeSetGroupAdd
     */
    public function testProductAttributeSetGroupRemove()
    {
        $apiMethod = 'product_attribute_set.groupRemove';
        $prevResponse = $this->prevCall($apiMethod, array('attributeGroupId' => self::$_prevAttributeSetGroupId));
        $currResponse = $this->currCall($apiMethod, array('attributeGroupId' => self::$_currAttributeSetGroupId));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test product attribute set attribute add method compatibility.
     * Scenario:
     * 1. Add an attribute to product attribute set created in 'testProductAttributeSetCreate' method at previous API.
     * 2. Add an attribute to product attribute set created in 'testProductAttributeSetCreate' method at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testProductAttributeSetCreate
     */
    public function testProductAttributeSetAttributeAdd()
    {
        $apiMethod = 'product_attribute_set.attributeAdd';
        $productAttributeIds = $this->_createProductAttributes();
        self::$_prevProductAttributeId = $productAttributeIds['prevProductAttributeId'];
        self::$_currProductAttributeId = $productAttributeIds['currProductAttributeId'];
        $prevResponse = $this->prevCall($apiMethod, array('productAttributeId' => self::$_prevProductAttributeId, 'attributeSetId' => self::$_prevAttributeSetId));
        $currResponse = $this->currCall($apiMethod, array('productAttributeId' => self::$_currProductAttributeId, 'attributeSetId' => self::$_currAttributeSetId));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test product attribute set attribute remove method compatibility.
     * Scenario:
     * 1. Remove product attribute, created in 'testProductAttributeSetAttributeAdd' method,
     * from attribute set, created in 'testProductAttributeSetCreate' method, at previous API.
     * 2. Remove product attribute, created in 'testProductAttributeSetAttributeAdd' method,
     * from attribute set, created in 'testProductAttributeSetCreate' method, at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testProductAttributeSetAttributeAdd
     * @depends testProductAttributeSetCreate
     */
    public function testProductAttributeSetAttributeRemove()
    {
        $apiMethod = 'product_attribute_set.attributeRemove';
        $prevResponse = $this->prevCall($apiMethod, array(
            'productAttributeId' => self::$_prevProductAttributeId,
            'attributeSetId' => self::$_prevAttributeSetId
        ));
        $currResponse = $this->currCall($apiMethod, array(
            'productAttributeId' => self::$_currProductAttributeId,
            'attributeSetId' => self::$_currAttributeSetId
        ));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test product attribute set remove method compatibility.
     * Scenario:
     * 1. Remove product attribute set, created in 'testProductAttributeSetCreate' method, at previous API.
     * 2. Remove product attribute set, created in 'testProductAttributeSetCreate' method, at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testProductAttributeSetCreate
     */
    public function testProductAttributeSetRemove()
    {
        $apiMethod = 'product_attribute_set.remove';
        $prevResponse = $this->prevCall($apiMethod, array('attributeSetId' => self::$_prevAttributeSetId));
        $currResponse = $this->currCall($apiMethod, array('attributeSetId' => self::$_currAttributeSetId));
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

}
