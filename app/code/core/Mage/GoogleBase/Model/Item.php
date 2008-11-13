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
 * @category   Mage
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Base Item Types Model
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Model_Item extends Mage_Core_Model_Abstract
{
    const ATTRIBUTES_REGISTRY_KEY = 'gbase_attributes_registry';
    const TYPES_REGISTRY_KEY = 'gbase_types_registry';

    protected function _construct()
    {
        parent::_construct();
        $this->_init('googlebase/item');
    }

    /**
     *  Load Item Model by Product
     *
     *  @param    Mage_Catalog_Model_Product $product
     *  @return	  Mage_GoogleBase_Model_Item
     */
    public function loadByProduct($product)
    {
        if (!$this->getProduct()) {
            $this->setProduct($product);
        }
        $this->getResource()->loadByProduct($this);
        return $this;
    }

    /**
     *  Return Service Item Instance
     *
     *  @param    none
     *  @return	  Mage_GoogleBase_Model_Service_Item
     */
    public function getServiceItem()
    {
        return Mage::getModel('googlebase/service_item');
    }

    /**
     *  Save item to Google Base
     *
     *  @param    none
     *  @return	  Mage_GoogleBase_Model_Item
     */
    public function insertItem ()
    {
        $this->_checkProduct();
        $product = $this->getProduct();
        $this->setProductId($product->getId());
        $this->setStoreId($product->getStoreId());
        $typeModel = $this->_getTypeModel();
        $serviceItem = $this->getServiceItem()
            ->setItem($this)
            ->setObject($product)
            ->setAttributeValues($this->_getAttributeValues())
            ->setItemType($typeModel->getGbaseItemtype())
            ->insert();

        $this->setTypeId($typeModel->getTypeId());
        return $this;
    }

    /**
     *  Update Item data
     *
     *  @param    none
     *  @return	  Mage_GoogleBase_Model_Item
     */
    public function updateItem ()
    {
        $this->_checkProduct();
        $product = $this->getProduct();
        $this->loadByProduct($product);
        if ($this->getId()) {
            $typeModel = $this->_getTypeModel();
            $serviceItem = $this->getServiceItem()
                ->setItem($this)
                ->setObject($product)
                ->setAttributeValues($this->_getAttributeValues())
                ->setItemType($typeModel->getGbaseItemtype())
                ->update();
        }
        return $this;
    }

    /**
     *  Delete Item from Google Base
     *
     *  @param    none
     *  @return	  Mage_GoogleBase_Model_Item
     */
    public function deleteItem ()
    {
        $serviceItem = $this->getServiceItem()
            ->setItem($this)
            ->delete();
        return $this;
    }

    /**
     *  Delete Item from Google Base
     *
     *  @param    none
     *  @return	  Mage_GoogleBase_Model_Item
     */
    public function hideItem ()
    {
        $serviceItem = $this->getServiceItem()
            ->setItem($this)
            ->hide();
        $this->setIsHidden(1);
        $this->save();
        return $this;
    }

    /**
     *  Delete Item from Google Base
     *
     *  @param    none
     *  @return	  Mage_GoogleBase_Model_Item
     */
    public function activateItem ()
    {
        $serviceItem = $this->getServiceItem()
            ->setItem($this)
            ->activate();
        $this->setIsHidden(0);
        $this->save();
        return $this;
    }

    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     */
    protected function _checkProduct()
    {
        if (!($this->getProduct() instanceof Mage_Catalog_Model_Product)) {
            Mage::throwException('Invalid Product Model for Google Base Item');
        }
    }

    /**
     *  Return Product attribute values array
     *
     *  @param    none
     *  @return	  array Product attribute values
     */
    protected function _getAttributeValues()
    {
        $result = array();
        $productAttributes = $this->_getProductAttributes();

        foreach ($this->_getAttributesCollection() as $attribute) {
            $attributeId = $attribute->getAttributeId();
            if (isset($productAttributes[$attributeId])) {
                $productAttribute = $productAttributes[$attributeId];
                $name = $attribute->getGbaseAttribute();
                $value = $productAttribute['value'];
                $type = Mage::getSingleton('googlebase/attribute')->getGbaseAttributeType($productAttribute['frontend_input']);
                $result[$name] = array(
                    'value'     => $value,
                    'type'      => $type
                );
            }
        }
        return $result;
    }

    /**
     *  Return Google Base Item Type Model for current Product Attribute Set
     *
     *  @param    none
     *  @return	  Mage_GoogleBase_Model_Type
     */
    protected function _getTypeModel()
    {
        $registry = Mage::registry(self::TYPES_REGISTRY_KEY);
        $attributeSetId = $this->getProduct()->getAttributeSetId();
        if (is_array($registry) && isset($registry[$attributeSetId])) {
            return $registry[$attributeSetId];
        }
        $model = Mage::getModel('googlebase/type')->loadByAttributeSetId($attributeSetId);
        $registry[$attributeSetId] = $model;
        Mage::unregister(self::TYPES_REGISTRY_KEY);
        Mage::register(self::TYPES_REGISTRY_KEY, $registry);
        return $model;
    }

    /**
     *  Return Product attributes array
     *
     *  @param    none
     *  @return	  array Product attributes
     */
    protected function _getProductAttributes()
    {
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        $result = array();
        foreach ($attributes as $attribute) {
            $value = $attribute->getFrontend()->getValue($product);
            if (is_string($value) && strlen($value) && $product->hasData($attribute->getAttributeCode())) {
                $result[$attribute->getAttributeId()] = array(
                   'label'          => $attribute->getFrontend()->getLabel(),
                   'value'          => $value,
                   'code'           => $attribute->getAttributeCode(),
                   'backend_type'   => $attribute->getBackendType(),
                   'frontend_input' => $attribute->getFrontendInput()
                );
            }
        }
        return $result;
    }

    /**
     *  Media files
     *
     *  @param    none
     *  @return	  void
     */
    protected function _getMediaImages ()
    {
        $product = $this->getProduct();
        $galleryData = $product->getData('media_gallery');

        if (!isset($galleryData['images']) || !is_array($galleryData['images'])) {
            return array();
        }

        $result = array();
        foreach ($galleryData['images'] as $image) {
            $image['url'] = Mage::getSingleton('catalog/product_media_config')
                ->getMediaUrl($image['file']);
            $result[] = $image;
        }
        return $result;
    }

    /**
     *  Return attribute collection for current Product Attribute Set
     *
     *  @param    none
     *  @return	  Mage_GoogleBase_Model_Mysql4_Attribute_Collection
     */
    protected function _getAttributesCollection()
    {
        $registry = Mage::registry(self::ATTRIBUTES_REGISTRY_KEY);
        $attributeSetId = $this->getProduct()->getAttributeSetId();
        if (is_array($registry) && isset($registry[$attributeSetId])) {
            return $registry[$attributeSetId];
        }
        $collection = Mage::getResourceModel('googlebase/attribute_collection')
            ->addAttributeSetFilter($attributeSetId)
            ->load();
        $registry[$attributeSetId] = $collection;
        Mage::unregister(self::ATTRIBUTES_REGISTRY_KEY);
        Mage::register(self::ATTRIBUTES_REGISTRY_KEY, $registry);
        return $collection;
    }
}