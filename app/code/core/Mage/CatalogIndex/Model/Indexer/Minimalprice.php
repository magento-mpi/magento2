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
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog indexer price processor
 *
 * @author Sasha Boyko <alex.boyko@varien.com>
 */
class Mage_CatalogIndex_Model_Indexer_Minimalprice extends Mage_CatalogIndex_Model_Indexer_Abstract
{
    protected $_customerGroups = array();
    protected $_runOnce = true;
    protected $_processChildren = false;

    protected function _construct()
    {
        $this->_init('catalogindex/indexer_minimalprice');
        $this->_currencyModel = Mage::getModel('directory/currency');
        $this->_customerGroups = Mage::getModel('customer/group')->getCollection();

        return parent::_construct();
    }

    public function getTierPriceAttribute()
    {
        $data = $this->getData('tier_price_attribute');
        if (is_null($data)) {
            $data = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'tier_price');
            $this->setData('tier_price_attribute', $data);
        }
        return $data;
    }

    public function getPriceAttribute()
    {
        $data = $this->getData('price_attribute');
        if (is_null($data)) {
            $data = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', 'price');
            $this->setData('price_attribute', $data);
        }
        return $data;
    }

    public function createIndexData(Mage_Catalog_Model_Product $object)
    {
        $searchEntityId = $object->getId();
        $priceAttributeId = $this->getTierPriceAttribute()->getId();
        if ($object->isGrouped()) {
            $priceAttributeId = $this->getPriceAttribute()->getId();
            $associated = $object->getTypeInstance()->getAssociatedProducts();
            $searchEntityId = array();

            foreach ($associated as $product)
                $searchEntityId[] = $product->getId();
        }


        $result = array();
        $data = array();

        $data['store_id'] = $object->getStoreId();
        $data['entity_id'] = $object->getId();

        $search['store_id'] = $object->getStoreId();
        $search['entity_id'] = $searchEntityId;
        $search['attribute_id'] = $priceAttributeId;

        foreach ($this->_customerGroups as $group) {
            $search['customer_group_id'] = $group->getId();
            $data['customer_group_id'] = $group->getId();

            $value = $this->_getResource()->getMinimalValue($search);
            if (is_null($value))
                continue;
            $data['value'] = $value;
            $result[] = $data;
        }

        return $result;
    }
}