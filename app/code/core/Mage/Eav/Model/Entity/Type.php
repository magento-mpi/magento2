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
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Entity type model
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author Moshe Gurvich <moshe@varien.com>
 */
class Mage_Eav_Model_Entity_Type extends Mage_Core_Model_Abstract
{

    /**
     * Enter description here...
     *
     * @var Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    protected $_attributes;

    /**
     * Enter description here...
     *
     * @var array
     */
    protected $_attributesBySet = array();

    /**
     * Enter description here...
     *
     * @var Mage_Eav_Model_Mysql4_Entity_Attribute_Set_Collection
     */
    protected $_sets;

    /**
     * Enter description here...
     *
     */
    protected function _construct()
    {
        $this->_init('eav/entity_type');
    }

    /**
     * Enter description here...
     *
     * @param string $code
     * @return Mage_Eav_Model_Entity_Type
     */
    public function loadByCode($code)
    {
        $this->_getResource()->loadByCode($this, $code);
        return $this;
    }

    /**
     * Retrieve entity type attributes collection
     *
     * @param   int $setId
     * @return  Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function getAttributeCollection($setId = null)
    {
        if (is_null($setId)) {
            if (is_null($this->_attributes)) {
                $this->_attributes = $this->_getAttributeCollection()
                    ->setEntityTypeFilter($this->getId());
            }
            $collection = $this->_attributes;
        }
        else {
            if (!isset($this->_attributesBySet[$setId])) {
                $this->_attributesBySet[$setId] = $this->_getAttributeCollection()
                    ->setEntityTypeFilter($this->getId())
                    ->setAttributeSetFilter($setId);
            }
            $collection = $this->_attributesBySet[$setId];
        }
        return $collection;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    protected function _getAttributeCollection()
    {
        $collection = Mage::getModel('eav/entity_attribute')->getCollection();
        if ($objectsModel = $this->getAttributeModel()) {
            $collection->setModel($objectsModel);
        }
        return $collection;
    }

    /**
     * Retrieve entity tpe sets collection
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Set_Collection
     */
    public function getAttributeSetCollection()
    {
        if (empty($this->_sets)) {
            $this->_sets = Mage::getModel('eav/entity_attribute_set')->getResourceCollection()
                ->setEntityTypeFilter($this->getId());
        }
        return $this->_sets;
    }

    /**
     * Enter description here...
     *
     * @param int $storeId
     * @return string
     */
    public function fetchNewIncrementId($storeId=null)
    {
        if (!$this->getIncrementModel()) {
            return false;
        }

        if (!$this->getIncrementPerStore()) {
            $storeId = 0;
        } elseif (is_null($storeId)) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Valid store_id is expected!'));
        }

        $entityStoreConfig = Mage::getModel('eav/entity_store')
            ->loadByEntityStore($this->getId(), $storeId);

        if (!$entityStoreConfig->getId()) {
            $entityStoreConfig
                ->setEntityTypeId($this->getId())
                ->setStoreId($storeId)
                ->setIncrementPrefix($storeId)
                ->save();
        }

        $incrementInstance = Mage::getModel($this->getIncrementModel())
            ->setPrefix($entityStoreConfig->getIncrementPrefix())
            ->setPadLength($entityStoreConfig->getIncrementPadLength())
            ->setPadChar($entityStoreConfig->getIncrementPadChar())
            ->setLastId($entityStoreConfig->getIncrementLastId());

        /**
         * do read lock on eav/entity_store to solve potential timing issues
         * (most probably already done by beginTransaction of entity save)
         */
        $incrementId = $incrementInstance->getNextId();
        $entityStoreConfig->setIncrementLastId($incrementId);
        $entityStoreConfig->save();

        return $incrementId;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getEntityIdField()
    {
        return $this->getData('entity_id_field');
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getEntityTable()
    {
        return $this->getData('entity_table');
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getValueTablePrefix()
    {
        return $this->getData('value_table_prefix');
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getDefaultAttributeSetId()
    {
        return $this->getData('default_attribute_set_id');
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getEntityTypeId()
    {
        return $this->getData('entity_type_id');
    }

}
