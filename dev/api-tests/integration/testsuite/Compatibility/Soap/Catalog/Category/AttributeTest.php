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
 * Test Category Attribute methods compatibility between previous and current API versions.
 */
class Compatibility_Soap_Catalog_Category_AttributeTest extends Compatibility_Soap_SoapAbstract
{
    /**
     * Attribute ID in previous API
     * @var int
     */
    protected static $_prevAttributeId;

    /**
     * Attribute ID in current API
     * @var int
     */
    protected static $_currAttributeId;

    /**
     * Test category attribute list method compatibility.
     * Scenario:
     * 1. Retrieve category attributes list in previous API.
     * 2. Retrieve category attributes list in current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     */
    public function testCatalogCategoryAttributeList()
    {
        $apiMethod = 'catalog_category_attribute.list';
        $prevResponse = $this->prevCall($apiMethod);
        $currResponse = $this->currCall($apiMethod);
        $this->_checkResponse($prevResponse, $currResponse, $apiMethod);
        self::$_prevAttributeId = $this->_getIsActiveAttributeId($prevResponse);
        self::$_currAttributeId = $this->_getIsActiveAttributeId($currResponse);
        $this->_checkVersionAttributeSignature($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test category attribute options method compatibility.
     * Scenario:
     * 1. Retrieve category attribute options in previous API.
     * 2. Retrieve category attribute options in current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCatalogCategoryAttributeList
     */
    public function testCatalogCategoryAttributeOptions()
    {
        $apiMethod = 'catalog_category_attribute.options';
        $prevOptionsList = $this->prevCall($apiMethod, array(self::$_prevAttributeId));
        $currOptionsList = $this->currCall($apiMethod, array(self::$_currAttributeId));
        $this->_checkResponse($prevOptionsList, $currOptionsList, $apiMethod);
        $this->_checkVersionSignature($prevOptionsList, $currOptionsList, $apiMethod);
    }

    /**
     * Returns is_active attribute Id.
     *
     * @param array $attributesList
     * @return int $attributeId
     */
    protected function _getIsActiveAttributeId($attributesList)
    {
        $attributeId = NULL;
        foreach ($attributesList as $attributeData) {
            if ($attributeData['code'] == 'is_active') {
                $attributeId = $attributeData['attribute_id'];
            }
        }
        return $attributeId;
    }

    /**
     * Compare existance of attributes and its fields in current and previous API.
     *
     * @param array $prevResponse
     * @param array $currResponse
     * @param string $apiMethod
     */
    protected function _checkVersionAttributeSignature($prevResponse, $currResponse, $apiMethod)
    {
        foreach ($prevResponse as $prevAttributeData) {
            $isAttributeFound = false;
            foreach ($currResponse as $currAttributeData) {
                if ($prevAttributeData['code'] == $currAttributeData['code']) {
                    $isAttributeFound = true;
                    foreach (array_keys($prevAttributeData) as $key) {
                        $this->assertArrayHasKey($key, $currAttributeData, 'Key ' . $key
                            . 'is missed in ' . $currAttributeData['code'] . ' attribute in current API in ' . $apiMethod
                            . ' method');
                    }
                }
            }
            $this->assertTrue($isAttributeFound, 'Attribute with code ' . $prevAttributeData['code']
                . ' is not found in current API in ' . $apiMethod . ' method');
        }
    }
}

