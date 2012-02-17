<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 for products collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Products_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Products_Rest
{
    /**
     * Pre-validate request data
     *
     * @param array $data
     * @param array $required
     * @param array $notEmpty
     */
    protected function _validate(array $data, array $required = array(), array $notEmpty = array())
    {
        parent::_validate($data, $required, $notEmpty);

        $setId = $data['set'];
        /** @var $entity Mage_Eav_Model_Entity_Type */
        $entity = Mage::getModel('eav/entity_type')->loadByCode(Mage_Catalog_Model_Product::ENTITY);
        /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
        $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($setId);
        if (!$attributeSet->getId() || $entity->getEntityTypeId() != $attributeSet->getEntityTypeId()) {
            $this->_critical('Invalid attribute set', self::RESOURCE_DATA_INVALID);
        }

        $type = $data['type'];
        $productTypes = Mage_Catalog_Model_Product_Type::getTypes();
        if (!array_key_exists($type, $productTypes)) {
            $this->_critical('Invalid product type', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        foreach ($entity->getAttributeCollection($setId) as $attribute) {
            $applicable = false;
            if (!$attribute->getApplyTo() || in_array($type, $attribute->getApplyTo())) {
                $applicable = true;
            }

            if (!$applicable && !$attribute->isStatic() && isset($data[$attribute->getAttributeCode()])) {
                $this->_error(sprintf('Attribute "%s" is not applicable for product type "%s"',
                    $attribute->getAttributeCode(), $productTypes[$type]['label']),
                    Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }

            if ($attribute->usesSource() && isset($data[$attribute->getAttributeCode()])) {
                $allowedValues = array();
                foreach ($attribute->getSource()->getAllOptions() as $option) {
                    $allowedValues[] = $option['value'];
                }
                if (!in_array($data[$attribute->getAttributeCode()], $allowedValues)) {
                    $this->_error(sprintf('Invalid value for attribute "%s"', $attribute->getAttributeCode()),
                        Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
                }
            }

            if ($attribute->getIsRequired() && $attribute->getIsVisible() && $applicable) {
                $required[] = $attribute->getAttributeCode();
            }
        }

        // Validate store input
        if (isset($data['store'])) {
            try {
                Mage::app()->getStore($data['store'])->getId();
            } catch (Mage_Core_Model_Store_Exception $e) {
                $this->_critical('Invalid store', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
        }

        parent::_validate($data, $required, $required);
    }

    /**
     * Create product
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        $required = array('type', 'set', 'sku');
        $notEmpty = array('type', 'set', 'sku');
        $this->_validate($data, $required, $notEmpty);

        $type = $data['type'];
        $set = $data['set'];
        $sku = $data['sku'];
        $productData = array_diff_key($data, array_flip(array('type', 'set', 'sku')));

        $store = isset($data['store']) ? $data['store'] : '';
        $storeId = Mage::app()->getStore($store)->getId();
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')
            ->setStoreId($storeId)
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);

        $this->_prepareDataForSave($product, $productData);
        try {
            $product->save();
            $this->_multicall($product->getId());
        } catch (Mage_Core_Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }

        return $this->_getLocation($product);
    }

   /**
    *  Set additional data before product saved
    *
    *  @param    Mage_Catalog_Model_Product $product
    *  @param    array $productData
    *  @return   object
    */
   protected function _prepareDataForSave($product, $productData)
   {
       if (isset($productData['website_ids']) && is_array($productData['website_ids'])) {
           $product->setWebsiteIds($productData['website_ids']);
       }
       /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
       foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
           //Unset data if object attribute has no value in current store
           if (Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID !== (int) $product->getStoreId()
               && !$product->getExistsStoreValueFlag($attribute->getAttributeCode())
               && !$attribute->isScopeGlobal()
           ) {
               $product->setData($attribute->getAttributeCode(), false);
           }

           if ($this->_isAllowedAttribute($attribute)) {
               if (isset($productData[$attribute->getAttributeCode()])) {
                   $product->setData(
                       $attribute->getAttributeCode(),
                       $productData[$attribute->getAttributeCode()]
                   );
               }
           }
       }

       if (isset($productData['stock_data']) && is_array($productData['stock_data'])) {
           $product->setStockData($productData['stock_data']);
       } else {
           $product->setStockData(array('use_config_manage_stock' => 0));
       }
   }

   /**
    * Check is attribute allowed
    *
    * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
    * @param array $attributes
    * @return boolean
    */
   protected function _isAllowedAttribute($attribute, $attributes = null)
   {
       if (is_array($attributes)
           && !( in_array($attribute->getAttributeCode(), $attributes)
                 || in_array($attribute->getAttributeId(), $attributes))) {
           return false;
       }

       $ignoredAttributeTypes = array();
       $ignoredAttributeCodes = array('entity_id', 'attribute_set_id', 'entity_type_id');

       return !in_array($attribute->getFrontendInput(), $ignoredAttributeTypes)
              && !in_array($attribute->getAttributeCode(), $ignoredAttributeCodes);
   }

   /**
    * Get location for given resource
    *
    * @param Mage_Catalog_Model_Abstract $product
    * @return string Location of new resource
    */
   protected function _getLocation(Mage_Catalog_Model_Abstract $product)
   {
       /** @var $config Mage_Api2_Model_Config */
       $config = Mage::getModel('api2/config');

       /** @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
       $apiTypeRoute = Mage::getModel('api2/route_apiType');

       $chain = $apiTypeRoute->chain(
           new Zend_Controller_Router_Route($config->getMainRoute('product'))
       );
       $params = array(
           'api_type' => $this->getRequest()->getApiType(),
           'id'       => $product->getId()
       );
       $uri = $chain->assemble($params);

       return '/'.$uri;
   }
}
