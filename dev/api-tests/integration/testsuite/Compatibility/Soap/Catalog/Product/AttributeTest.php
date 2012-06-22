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
class Compatibility_Soap_Catalog_Product_AttributeTest extends Magento_Test_Webservice_Compatibility
{
    /**
     * Product Attribute created at previous API
     * @var int
     */
    protected static $_prevProductAttributeId;

    /**
     * Product Attribute created at current API
     * @var int
     */
    protected static $_currProductAttributeId;

    /**
     * Product Attribute Option created at previous API
     * @var int
     */
    protected static $_prevProductAttributeOptionId;

    /**
     * Product Attribute Option created at current API
     * @var int
     */
    protected static $_currProductAttributeOptionId;

    /**
     * Test product attribute current store method compatibility.
     * Scenario:
     * 1. Get product attribute current store at previous API.
     * 2. Get product attribute current store  at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     */
    public function testProductAttributeCurrentStore()
    {
        $apiMethod = 'catalog_product_attribute.currentStore';
        $prevResponse = $this->prevCall($apiMethod);
        $currResponse = $this->currCall($apiMethod);
        $this->_checkVersionCompatibility($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test product attribute current store method compatibility.
     * Scenario:
     * 1. Create product attribute at previous API.
     * 2. Create product attribute at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     */
    public function testProductAttributeCreate()
    {
        $apiMethod = 'catalog_product_attribute.create';
        $attributeData = array(
            'attribute_code' => 'test_attribute' . uniqid(),
            'frontend_input' => 'select',
            'scope' => '1',
            'default_value' => '1',
            'is_unique' => 0,
            'is_required' => 0,
            'apply_to' => array('simple'),
            'is_configurable' => 0,
            'is_searchable' => 0,
            'is_visible_in_advanced_search' => 0,
            'is_comparable' => 0,
            'is_used_for_promo_rules' => 0,
            'is_visible_on_front' => 0,
            'used_in_product_listing' => 0,
            'additional_fields' => array(),
            'frontend_label' => array(array('store_id' => '0', 'label' => 'some label'))
        );
        self::$_prevProductAttributeId = $this->prevCall($apiMethod, array('data' => $attributeData));
        self::$_currProductAttributeId = $this->currCall($apiMethod, array('data' => $attributeData));
        $this->_checkVersionCompatibility(self::$_prevProductAttributeId, self::$_currProductAttributeId, $apiMethod);
    }

    /**
     * Test product attribute current store method compatibility.
     * Scenario:
     * 1. Get product attribute list at previous API.
     * 2. Get product attribute list at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     */
    public function testProductAttributeList()
    {
        $entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');
        $apiMethod = 'catalog_product_attribute.list';
        $prevResponse = $this->prevCall($apiMethod, $entityType->getDefaultAttributeSetId());
        $currResponse = $this->currCall($apiMethod, $entityType->getDefaultAttributeSetId());
        $this->_checkVersionCompatibility($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test product attribute current store method compatibility.
     * Scenario:
     * 1. Get product attribute options at previous API.
     * 2. Get product attribute options at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     */
    public function testProductAttributeOptions()
    {
        $apiMethod = 'catalog_product_attribute.options';
        $prevResponse = $this->prevCall($apiMethod, array('attributeId' => self::$_prevProductAttributeId));
        $currResponse = $this->currCall($apiMethod, array('attributeId' => self::$_currProductAttributeId));
        $this->_checkVersionCompatibility($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test product attribute current store method compatibility.
     * Scenario:
     * 1. Add product attribute option at previous API.
     * 2. Add product attribute option at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testProductAttributeCreate
     */
    public function testProductAttributeAddOption()
    {
        $apiMethod = 'catalog_product_attribute.options';
        $attributeLabel = array(array(
            'store_id' => array("0"),
            "value" => "test_attribute_code label"
        ));
        $attributeData = array(
            'label' => $attributeLabel,
            'order' => "1",
            'is_default' => "1"
        );
        self::$_prevProductAttributeOptionId = $this->prevCall($apiMethod, self::$_prevProductAttributeId, $attributeData);
        self::$_currProductAttributeOptionId = $this->currCall($apiMethod, self::$_currProductAttributeId, $attributeData);
        $this->_checkVersionCompatibility(self::$_prevProductAttributeOptionId, self::$_currProductAttributeOptionId, $apiMethod);
    }

    /**
     * Test product attribute current store method compatibility.
     * Scenario:
     * 1. Get product attribute info at previous API.
     * 2. Get product attribute info at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testProductAttributeCreate
     */
    public function testProductAttributeInfo()
    {
        $apiMethod = 'catalog_product_attribute.info';
        $prevResponse = $this->prevCall($apiMethod, self::$_prevProductAttributeId);
        $currResponse = $this->currCall($apiMethod, self::$_currProductAttributeId);
        $this->_checkVersionCompatibility($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test product attribute current store method compatibility.
     * Scenario:
     * 1. Remove product attribute option added in testProductAttributeAddOption at previous API.
     * 2. Remove product attribute option added in testProductAttributeAddOption at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testProductAttributeAddOption
     */
    public function testProductAttributeOptionRemove()
    {
        $apiMethod = 'catalog_product_attribute.removeOption';
        $prevResponse = $this->prevCall($apiMethod, self::$_prevProductAttributeId, self::$_prevProductAttributeOptionId);
        $currResponse = $this->currCall($apiMethod, self::$_currProductAttributeId, self::$_currProductAttributeOptionId);
        $this->_checkVersionCompatibility($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test product attribute current store method compatibility.
     * Scenario:
     * 1. Get product attribute types at previous API.
     * 2. Get product attribute types at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     */
    public function testProductAttributeTypes()
    {
        $apiMethod = 'catalog_product_attribute.types';
        $prevResponseSignature = $this->prevCall($apiMethod);
        $currResponseSignature = $this->currCall($apiMethod);
        $this->assertEquals($prevResponseSignature, $currResponseSignature,
            "The signature of $apiMethod has changed in the new API version.");
    }

    /**
     * Test product attribute current store method compatibility.
     * Scenario:
     * 1. Update product attribute created in testProductAttributeCreate at previous API.
     * 2. Update product attribute created in testProductAttributeCreate at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testProductAttributeCreate
     */
    public function testProductAttributeUpdate()
    {
        $apiMethod = 'catalog_product_attribute.update';
        $prevAttributeData = array(
            'attribute_code' => self::$_prevProductAttributeId,
            'frontend_input' => 'text',
            'scope' => '1',
            'default_value' => '1',
            'is_unique' => 0,
            'is_required' => 0,
            'apply_to' => array('simple'),
            'is_configurable' => 0,
            'is_searchable' => 0,
            'is_visible_in_advanced_search' => 0,
            'is_comparable' => 0,
            'is_used_for_promo_rules' => 0,
            'is_visible_on_front' => 0,
            'used_in_product_listing' => 0,
            'additional_fields' => array(),
            'frontend_label' => array(array('store_id' => '0', 'label' => 'some label'))
        );
        $currAttributeData = array(
            'attribute_code' => self::$_currProductAttributeId,
            'frontend_input' => 'text',
            'scope' => '1',
            'default_value' => '1',
            'is_unique' => 0,
            'is_required' => 0,
            'apply_to' => array('simple'),
            'is_configurable' => 0,
            'is_searchable' => 0,
            'is_visible_in_advanced_search' => 0,
            'is_comparable' => 0,
            'is_used_for_promo_rules' => 0,
            'is_visible_on_front' => 0,
            'used_in_product_listing' => 0,
            'additional_fields' => array(),
            'frontend_label' => array(array('store_id' => '0', 'label' => 'some label'))
        );
        $prevResponse = $this->prevCall($apiMethod, $prevAttributeData);
        $currResponse = $this->currCall($apiMethod, $currAttributeData);
        $this->_checkVersionCompatibility($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test product attribute current store method compatibility.
     * Scenario:
     * 1. Remove product attribute created in testProductAttributeCreate at previous API.
     * 2. Remove product attribute created in testProductAttributeCreate at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     *@depends testProductAttributeCreate
     */
    public function testProductAttributeRemove()
    {
        $apiMethod = 'catalog_product_attribute.remove';
        $prevResponse = $this->prevCall($apiMethod, array('attribute' => self::$_prevProductAttributeId));
        $currResponse = $this->currCall($apiMethod, array('attribute' => self::$_currProductAttributeId));
        $this->_checkVersionCompatibility($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Compare types of API responses (current and previous versions)
     *
     * @param mixed $prevResponse
     * @param mixed $currResponse
     * @param string $apiMethod
     */
    protected function _checkVersionCompatibility($prevResponse, $currResponse, $apiMethod)
    {
        $this->assertInternalType(gettype($prevResponse), $currResponse,
            "The signature of $apiMethod has changed in the new API version.");
    }
}
