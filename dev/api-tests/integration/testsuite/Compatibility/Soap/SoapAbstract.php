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
     * @throws Exception
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

        foreach (array_keys($prevResponse) as $key) {
            $this->assertArrayHasKey($key, $currResponse,
                sprintf('Key "%s" was not found in current "%s" API response.', $key, $apiMethod));
        }
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
            'parentId' => Mage_Catalog_Model_Category::TREE_ROOT_ID,
            'categoryData' => $categoryData
        ));
        $categoryIds['currCategoryId'] = $this->currCall('catalog_category.create', array(
            'parentId' => Mage_Catalog_Model_Category::TREE_ROOT_ID,
            'categoryData' => $categoryData
        ));
        return $categoryIds;
    }

    /**
     * Create product attributes in current and previous API and return IDs
     *
     * @return array $productAttributeIds
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
            'frontend_label' => array('frontend_label' => array('store_id' => '0', 'label' => 'some label'))
        );
        $productAttributeIds['prevProductAttributeId'] = $this->prevCall($apiMethod, array('data' => $attributeData));
        $productAttributeIds['currProductAttributeId'] = $this->currCall($apiMethod, array('data' => $attributeData));
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
                'weight' => 1
            )
        );
        $productIds['prevProductId'] = $this->prevCall('catalog_product.create', $productData);
        $productIds['currProductId'] = $this->currCall('catalog_product.create', $productData);
        return $productIds;
    }

    /**
     * Create customers in current and previous API and return IDs
     *
     * @return array $customerIds
     */
    protected function _createCustomers()
    {
        $customerIds = array();
        $apiMethod = 'customer.create';
        $customerData = array('customerData' => array(
            'email' => 'customer-mail' . uniqid() . '@example.org',
            'firstname' => 'Test Name',
            'lastname' => 'Test Last Name',
            'password' => 'password',
            'website_id' => 1,
            'store_id' => 1,
            'group_id' => 1
        ));
        $customerIds['prevCustomerId'] = $this->prevCall($apiMethod, $customerData);
        $customerIds['currCustomerId'] = $this->currCall($apiMethod, $customerData);
        return $customerIds;
    }

    /**
     * Create shopping carts in current and previous API and return IDs
     *
     * @return array $ShoppingCartIds
     */
    protected function _createShoppingCarts()
    {
        $shoppingCartIds = array();
        $apiMethod = 'cart.create';
        $shoppingCartIds['prevShoppingCartId'] = $this->prevCall($apiMethod);
        $shoppingCartIds['currShoppingCartId'] = $this->currCall($apiMethod);
        return $shoppingCartIds;
    }
}
