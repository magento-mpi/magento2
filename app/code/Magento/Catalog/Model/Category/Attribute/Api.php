<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog category attribute api
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Category_Attribute_Api extends Magento_Catalog_Model_Api_Resource
{
    /**
     * @param Magento_Catalog_Helper_Product $catalogProduct
     */
    public function __construct(
        Magento_Catalog_Helper_Product $catalogProduct
    ) {
        parent::__construct($catalogProduct);
        $this->_storeIdSessionField = 'category_store_id';
    }

    /**
     * Retrieve category attributes
     *
     * @return array
     */
    public function items()
    {
        $attributes = Mage::getModel('Magento_Catalog_Model_Category')->getAttributes();
        $result = array();

        foreach ($attributes as $attribute) {
            /* @var $attribute Magento_Catalog_Model_Resource_Eav_Attribute */
            if ($this->_isAllowedAttribute($attribute)) {
                if (!$attribute->getId() || $attribute->isScopeGlobal()) {
                    $scope = 'global';
                } elseif ($attribute->isScopeWebsite()) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $result[] = array(
                    'attribute_id' => $attribute->getId(),
                    'code'         => $attribute->getAttributeCode(),
                    'type'         => $attribute->getFrontendInput(),
                    'required'     => $attribute->getIsRequired(),
                    'scope'        => $scope
                );
            }
        }

        return $result;
    }

    /**
     * Retrieve category attribute options
     *
     * @param int|string $attributeId
     * @param string|int $store
     * @return array
     */
    public function options($attributeId, $store = null)
    {
        $attribute = Mage::getModel('Magento_Catalog_Model_Category')
            ->setStoreId($this->_getStoreId($store))
            ->getResource()
            ->getAttribute($attributeId);

        if (!$attribute) {
            $this->_fault('not_exists');
        }

        $result = array();
        if ($attribute->usesSource()) {
            foreach ($attribute->getSource()->getAllOptions(false) as $optionId=>$optionValue) {
                if (is_array($optionValue)) {
                    $result[] = $optionValue;
                } else {
                    $result[] = array(
                        'value' => $optionId,
                        'label' => $optionValue
                    );
                }
            }
        }

        return $result;
    }
} // Class Magento_Catalog_Model_Category_Attribute_Api End
