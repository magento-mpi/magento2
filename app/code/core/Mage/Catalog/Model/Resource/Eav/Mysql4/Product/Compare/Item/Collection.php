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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog compare item collection model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Compare_Item_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{

    protected $_customerId = 0;
    protected $_visitorId  = 0;

    protected function _construct()
    {
        $this->_init('catalog/product_compare_item', 'catalog/product');
    }

    public function setCustomerId($customerId)
    {
        $this->_customerId = $customerId;
        $this->_addJoinToSelect();
        return $this;
    }

    public function setVisitorId($visitorId)
    {
        $this->_visitorId = $visitorId;
        $this->_addJoinToSelect();
        return $this;
    }

    public function getCustomerId()
    {
        return $this->_customerId;
    }

    public function getVisitorId()
    {
        return $this->_visitorId;
    }

    public function getConditionForJoin()
    {
        if($this->getCustomerId()) {
            return array('customer_id'=>$this->getCustomerId());
        }

        if($this->getVisitorId()) {
            return array('visitor_id'=>$this->getVisitorId());
        }

        return null;
    }

    public function _addJoinToSelect()
    {
        $this->joinField(
            'catalog_compare_item_id',
            'catalog/compare_item',
            'catalog_compare_item_id',
            'product_id=entity_id',
            $this->getConditionForJoin()
        );
        $this->joinTable(
            'catalog/compare_item',
            'catalog_compare_item_id=catalog_compare_item_id',
            array('product_id', 'customer_id', 'visitor_id'));
        $this->addStoreFilter();
        return $this;
    }

    public function loadComaparableAttributes()
    {
        $compareTable = Mage::getSingleton('core/resource')->getTableName('catalog/compare_item');
        if($this->getCustomerId()) {
            $compareCondition = 'customer_id='.$this->getCustomerId();
        } else {
            $compareCondition = 'visitor_id='.$this->getVisitorId();
        }


        $attributesCollection = $this->getEntity()->getConfig()->getAttributeCollection();

        $websiteId = Mage::app($this->getStoreId())->getStore()->getWebsiteId();

        $select = $this->getConnection()->select()
            ->from(array('entity'=>$this->getEntity()->getEntityTable()), 'attribute_set_id')
            ->join(array('website'=>$this->getTable('catalog/product_website')),
                'website.product_id=entity.entity_id AND website.website_id='.(int)$websiteId,
                array()
            )
            ->join(array('compare'=>$compareTable),
                'compare.product_id=entity.entity_id AND compare.'.$compareCondition,
                array()
            )
            ->group('entity.attribute_set_id');

        $setIds = $this->getConnection()->fetchCol($select);
        if(sizeof($setIds)==0) {
            return $this;
        }

        $attributesCollection->setAttributeSetsFilter($setIds)
            ->addVisibleFilter()
            ->addFieldToFilter('is_comparable', 1)
            ->load();

        foreach ($attributesCollection->getItems() as $attribute) {
            $this->getEntity()->getAttribute($attribute);
            $this->addAttributeToSelect($attribute->getAttributeCode());
        }

        return $this;
    }

    public function useProductItem()
    {
        $this->setObject('catalog/product');
        return $this;
    }

    public function getProductIds() {
        $ids = array();
        foreach ($this->getItems() as $item) {
            $ids[] = $item->getProductId();
        }

        return $ids;
    }
}