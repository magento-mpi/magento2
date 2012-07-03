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
 * SoapAbstract class.
 */
abstract class Compatibility_Soap_SoapAbstract extends Magento_Test_Webservice_Compatibility
{
    /**
     * Compare types of API responses (current and previous versions)
     *
     * @param mixed $prevResponse
     * @param mixed $currResponse
     * @param string $apiMethod
     */
    protected function _checkVersionType($prevResponse, $currResponse, $apiMethod)
    {
        $this->assertInternalType(gettype($prevResponse), $currResponse,
            "The type of $apiMethod has changed in the new API version.");
    }

    /**
     *  Check API responses are not empty (current and previous versions)
     *
     * @param mixed $prevResponse
     * @param mixed $currResponse
     * @param string $apiMethod
     */
    protected function _checkResponse($prevResponse, $currResponse, $apiMethod)
    {
        if (empty($prevResponse) || empty($currResponse)) {
            throw new Exception("Response of $apiMethod is expected to be not empty");
        }
    }

    /**
     * Compare types of API responses (current and previous versions)
     *
     * @param mixed $prevResponse
     * @param mixed $currResponse
     * @param string $apiMethod
     */
    protected function _checkVersionSignature($prevResponse, $currResponse, $apiMethod)
    {
        $prevResponseSignature = array_keys($prevResponse);
        $currResponseSignature = array_keys($currResponse);
        $this->assertEquals($prevResponseSignature, $currResponseSignature,
            "The signature of $apiMethod has changed in the new API version.");
    }

    /**
     * Create categories in current and previous API and return IDs
     *
     * @return array $categoryId
     */
    protected function _createCategories()
    {
        $categoryIds = array();
        $categoryData = array(
            'name' => 'Category ' . uniqid(),
            'is_active' => '1',
            'include_in_menu' => '1',
            'available_sort_by' => array('position', 'name', 'price'),
            'default_sort_by' => 'position'
        );
        $categoryIds['prevCategoryId'] = $this->prevCall('catalog_category.create', array(
            Mage_Catalog_Model_Category::TREE_ROOT_ID,
            $categoryData
        ));
        $categoryIds['currCategoryId'] = $this->currCall('catalog_category.create', array(
            Mage_Catalog_Model_Category::TREE_ROOT_ID,
            $categoryData
        ));
        return $categoryIds;
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
    public function _createProductAttributes()
    {
        $apiMethod = 'catalog_product_attribute.create';
        $productAttributeIds = array();
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
        $productAttributeIds['prevProductAttributeId'] = $this->prevCall($apiMethod, array('data' => $attributeData));
        $productAttributeIds['currProductAttributeId'] = $this->currCall($apiMethod, array('data' => $attributeData));
        //$this->_checkVersionType(self::$_prevProductAttributeId, self::$_currProductAttributeId, $apiMethod);
        return $productAttributeIds;
    }


    /**
     * Create products in current and previous API and return IDs
     *
     * @return array $productId
     */
    protected function _createProducts()
    {
        $productIds = array();
        $entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('catalog_product');
        $productData = array(
            'type' => 'simple',
            'set' => $entityType->getDefaultAttributeSetId(),
            'sku' => 'compatibility-' . uniqid(),
            'productData' => array(
                'name' => 'Compatibility Test ' . uniqid(),
                'description' => 'Test description',
                'short_description' => 'Test short description',
                'status' => 1,
                'visibility' => 4,
                'price' => 9.99,
                'tax_class_id' => 2,
                'weight' => 1,
            )
        );
        $productIds['prevProductId'] = $this->prevCall('catalog_product.create', $productData);
        $productIds['currProductId'] = $this->currCall('catalog_product.create', $productData);
        return $productIds;
    }
}
