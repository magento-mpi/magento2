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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer Form Attribute Resource Collection
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Entity_Form_Attribute_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Current store instance
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Eav Entity Type instance
     *
     * @var Mage_Eav_Model_Entity_Type
     */
    protected $_entityType;

    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('eav/attribute', 'customer/form_attribute');
    }

    /**
     * Set current store to collection
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return Mage_Customer_Model_Entity_Form_Attribute_Collection
     */
    public function setStore($store)
    {
        $this->_store = Mage::app()->getStore($store);
        return $this;
    }

    /**
     * Return current store instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = Mage::app()->getStore();
        }
        return $this->_store;
    }

    /**
     * Set entity type instance to collection
     *
     * @param Mage_Eav_Model_Entity_Type|string|int $entityType
     * @return Mage_Customer_Model_Entity_Form_Attribute_Collection
     */
    public function setEntityType($entityType)
    {
        $this->_entityType = Mage::getSingleton('eav/config')->getEntityType($entityType);
        return $this;
    }

    /**
     * Return current entity type instance
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->setEntityType('customer');
        }
        return $this->_entityType;
    }

    /**
     * Add Form Code filter to collection
     *
     * @param string $code
     * @return Mage_Customer_Model_Entity_Form_Attribute_Collection
     */
    public function addFormCodeFilter($code)
    {
        return $this->addFieldToFilter('main_table.form_code', $code);
    }

    /**
     * Set order by attribute sort order
     *
     * @param string $direction
     * @return Mage_Customer_Model_Entity_Form_Attribute_Collection
     */
    public function setSortOrder($direction = self::SORT_ORDER_ASC)
    {
        return $this->setOrder('aa.sort_order', $direction);
    }

    /**
     * Add joins to select
     *
     * @return Mage_Customer_Model_Entity_Form_Attribute_Collection
     */
    protected function _beforeLoad()
    {
        $entityType = $this->getEntityType();
        $this->setItemObjectClass($entityType->getAttributeModel());
        $this->_select->join(
            array('ea' => $this->getTable('eav/attribute')),
            'main_table.attribute_id = ea.attribute_id',
            Zend_Db_Select::SQL_WILDCARD
        );

        // join additional attribute data table
        $additionalTable = $entityType->getAdditionalAttributeTable();
        if ($additionalTable) {
            $this->_select->join(
                array('aa' => $this->getTable($additionalTable)),
                'ea.attribute_id = aa.attribute_id',
                Zend_Db_Select::SQL_WILDCARD
            );
        }

        // add store attribute label
        $store = $this->getStore();
        if ($store->isAdmin()) {
            $this->_select->columns(array('store_label' => 'ea.frontend_label'));
        } else {
            $this->_select->joinLeft(
                array('al' => $this->getTable('eav/attribute_label')),
                'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int) $store->getId(),
                array('store_label' => new Zend_Db_Expr('IFNULL(al.value, ea.frontend_label)'))
            );
        }

        // add entity type filter
        $this->_select->where('ea.entity_type_id = ?', $entityType->getId());

        return parent::_beforeLoad();
    }
}
