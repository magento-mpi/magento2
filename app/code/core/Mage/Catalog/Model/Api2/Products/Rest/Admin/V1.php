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
 * @package     Mage_Review
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
     * Get resource static attributes
     *
     * @return array
     */
    protected function _getStaticAttributes()
    {
        return $this->getConfig()->getResourceAttributes($this->getResourceType());
    }

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

        $setAttributes = array();
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        foreach ($entity->getAttributeCollection($setId) as $attribute) {
            $setAttributes[] = $attribute->getAttributeCode();
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

        // Check if there are attributes in request that does not belong to provided attribute set
        $inputAttributes = array_diff_key($data, $this->_getStaticAttributes()); // Skip static attributes
        $wrongAttributes = array_diff(array_keys($inputAttributes), $setAttributes);
        foreach ($wrongAttributes as $attributeCode) {
            $this->_error(sprintf('Attribute "%s" is not from set #%d', $attributeCode, $setId),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
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
        $store = isset($data['store']) ? $data['store'] : '';
        $productData = array_diff_key($data, array_flip(array('type', 'set', 'sku')));

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product');

        try {
            $storeId = Mage::app()->getStore($store)->getId();
        } catch (Mage_Core_Model_Store_Exception $e) {
            $this->_critical(self::RESOURCE_DATA_INVALID);    //store_not_exists
        }

        $product->setStoreId($storeId)
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);

        if (!isset($productData['stock_data']) || !is_array($productData['stock_data'])) {
            //Set default stock_data if not exist in product data
            $product->setStockData(array('use_config_manage_stock' => 0));
        }

        $this->_prepareDataForSave($product, $productData);

        try {
            /**
             * @TODO implement full validation process with errors returning which are ignoring now
             * @TODO see Mage_Catalog_Model_Product::validate()
             */
            if (is_array($errors = $product->validate())) {
                foreach($errors as $code => $error) {
                    if ($error === true) {
                        $this->_error(
                            sprintf('Attribute "%s" is invalid.', $code),
                            Mage_Api2_Model_Server::HTTP_BAD_REQUEST
                        );   //data_invalid
                    }
                }
                $this->_critical(self::RESOURCE_DATA_INVALID);    //data_invalid
            }

            $product->save();
            $this->_multicall($product->getId());
        } catch (Mage_Core_Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);    //data_invalid
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
               } elseif (isset($productData['additional_attributes']['single_data'][$attribute->getAttributeCode()])) {
                   $product->setData(
                       $attribute->getAttributeCode(),
                       $productData['additional_attributes']['single_data'][$attribute->getAttributeCode()]
                   );
               } elseif (isset($productData['additional_attributes']['multi_data'][$attribute->getAttributeCode()])) {
                   $product->setData(
                       $attribute->getAttributeCode(),
                       $productData['additional_attributes']['multi_data'][$attribute->getAttributeCode()]
                   );
               }
           }
       }

       if (isset($productData['categories']) && is_array($productData['categories'])) {
           $product->setCategoryIds($productData['categories']);
       }

       if (isset($productData['websites']) && is_array($productData['websites'])) {
           foreach ($productData['websites'] as &$website) {
               if (is_string($website)) {
                   try {
                       $website = Mage::app()->getWebsite($website)->getId();
                   } catch (Exception $e) { }
               }
           }
           $product->setWebsiteIds($productData['websites']);
       }

       if (Mage::app()->isSingleStoreMode()) {
           $product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
       }

       if (isset($productData['stock_data']) && is_array($productData['stock_data'])) {
           $product->setStockData($productData['stock_data']);
       }

       if (isset($productData['tier_price']) && is_array($productData['tier_price'])) {
            $tierPrices = Mage::getModel('catalog/product_attribute_tierprice_api')
                ->prepareTierPrices($product, $productData['tier_price']);
            $product->setData(Mage_Catalog_Model_Product_Attribute_Tierprice_Api::ATTRIBUTE_CODE, $tierPrices);
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
