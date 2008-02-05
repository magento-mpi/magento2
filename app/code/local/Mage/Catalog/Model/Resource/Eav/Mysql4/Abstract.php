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
 * Catalog entity abstract model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Moshe Gurvich <moshe@varien.com>
 */
abstract class Mage_Catalog_Model_Resource_Eav_Mysql4_Abstract extends Mage_Eav_Model_Entity_Abstract
{
    /**
     * Current store id to retrieve entity for
     *
     * @var integer
     */
    protected $_storeId;

    /**
     * Current store to retrieve entity for
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Store Ids that share data for this entity
     *
     * @var array
     */
    protected $_sharedStoreIds=array();

    /**
     * Retrieve select object for loading base entity row
     *
     * @param   Varien_Object $object
     * @param   mixed $rowId
     * @return  Zend_Db_Select
     */
    protected function _getLoadRowSelect($object, $rowId)
    {
        $select = $this->_read->select()
            ->from($this->getEntityTable())
            ->where($this->getEntityIdField()."=?", $rowId);

        /*if ($this->getUseDataSharing()) {
            $select->where("store_id in (?)", $this->getSharedStoreIds());
        }*/

        return $select;
    }

    /**
     * Retrieve select object for loading entity attributes values
     *
     * @param   Varien_Object $object
     * @param   mixed $rowId
     * @return  Zend_Db_Select
     */
    protected function _getLoadAttributesSelect($object, $table)
    {
        $select = $this->_read->select()
            ->from($table)
            ->where($this->getEntityIdField() . '=?', $object->getId());
            /*->where("store_id=?", $storeId)*/;
        return $select;
    }







































    /**
     * Retrieve whether to support data sharing between stores for this entity
     *
     * Basically that means 2 things:
     * - entity table has store_id field which describes the originating store
     * - store_id is being filtered by all participating stores in share
     *
     * @return boolean
     */
    public function getUseDataSharing()
    {
        return $this->getConfig()->getIsDataSharing();
    }

    /**
     * Set store for which entity will be retrieved
     *
     * @param integer|string|Mage_Core_Model_Store $store
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function setStore($storeId=null)
    {
        $this->_store = Mage::app()->getStore($storeId);
        $this->_storeId = $this->_store->getId();

        $this->_sharedStoreIds = $this->getUseDataSharing() ? $this->_store->getDatashareStores($this->getConfig()->getDataSharingKey()) : false;
        if (empty($this->_sharedStoreIds)) {
            $this->_sharedStoreIds = array($this->_storeId);
        }

        return $this;
    }

    /**
     * Get current store id
     *
     * @return integer
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->setStore();
        }
        return $this->_storeId;
    }

    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->setStore();
        }
        return $this->_store;
    }

    /**
     * Enter description here...
     *
     * @return array|false
     */
    public function getSharedStoreIds()
    {
        if (empty($this->_sharedStoreIds)) {
            $this->setStore();
        }
        return $this->_sharedStoreIds;
    }





































    protected function _collectOrigData($object)
    {
        $this->loadAllAttributes($object);

        if ($this->getUseDataSharing()) {
            $storeId = $object->getStoreId();
        } else {
            $storeId = $this->getStoreId();
        }

        $allStores = Mage::getConfig()->getStoresConfigByPath('system/store/id', array(), 'code');
echo "<pre>".print_r($allStores ,1)."</pre>"; exit;
        $data = array();

        foreach ($this->getAttributesByTable() as $table=>$attributes) {
            $entityIdField = current($attributes)->getBackend()->getEntityIdField();

            $select = $this->_read->select()
                ->from($table)
                ->where($this->getEntityIdField()."=?", $object->getId());

            $where = $this->_read->quoteInto("store_id=?", $storeId);

            $globalAttributeIds = array();
            foreach ($attributes as $attrCode=>$attr) {
                if ($attr->getIsGlobal()) {
                    $globalAttributeIds[] = $attr->getId();
                }
            }
            if (!empty($globalAttributeIds)) {
                $where .= ' or '.$this->_read->quoteInto('attribute_id in (?)', $globalAttributeIds);
            }
            $select->where($where);

            $values = $this->_read->fetchAll($select);

            if (empty($values)) {
                continue;
            }
            foreach ($values as $row) {
                $data[$this->getAttribute($row['attribute_id'])->getName()][$row['store_id']] = $row;
            }
            foreach ($attributes as $attrCode=>$attr) {

            }
        }

        return $data;
    }

}
