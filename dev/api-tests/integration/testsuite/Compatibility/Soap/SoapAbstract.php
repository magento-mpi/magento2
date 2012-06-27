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
