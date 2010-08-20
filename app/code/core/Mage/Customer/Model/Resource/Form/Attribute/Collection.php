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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer Form Attribute Resource Collection
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Resource_Form_Attribute_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
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
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('eav/attribute', 'customer/form_attribute');
    }

    /**
     * Set current store to collection
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return Mage_Customer_Model_Resource_Form_Attribute_Collection
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
        if ($this->_store === null) {
            $this->_store = Mage::app()->getStore();
        }
        return $this->_store;
    }

    /**
     * Set entity type instance to collection
     *
     * @param Mage_Eav_Model_Entity_Type|string|int $entityType
     * @return Mage_Customer_Model_Resource_Form_Attribute_Collection
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
        if ($this->_entityType === null) {
            $this->setEntityType('customer');
        }
        return $this->_entityType;
    }

    /**
     * Add Form Code filter to collection
     *
     * @param string $code
     * @return Mage_Customer_Model_Resource_Form_Attribute_Collection
     */
    public function addFormCodeFilter($code)
    {
        return $this->addFieldToFilter('main_table.form_code', $code);
    }

    /**
     * Set order by attribute sort order
     *
     * @param string $direction
     * @return Mage_Customer_Model_Resource_Form_Attribute_Collection
     */
    public function setSortOrder($direction = self::SORT_ORDER_ASC)
    {
        $this->setOrder('ea.is_user_defined', self::SORT_ORDER_ASC);
        return $this->setOrder('ca.sort_order', $direction);
    }

    /**
     * Add joins to select
     *
     * @return Mage_Customer_Model_Resource_Form_Attribute_Collection
     */
    protected function _beforeLoad()
    {
        $select     = $this->getSelect();
        $connection = $this->getConnection();
        $entityType = $this->getEntityType();
        $this->setItemObjectClass($entityType->getAttributeModel());

        $eaColumns  = array();
        $caColumns  = array();
        $saColumns  = array();

        $eaDescribe = $connection->describeTable($this->getTable('eav/attribute'));
        unset($eaDescribe['attribute_id']);
        foreach (array_keys($eaDescribe) as $columnName) {
            $eaColumns[$columnName] = $columnName;
        }

        $select->join(
            array('ea' => $this->getTable('eav/attribute')),
            'main_table.attribute_id = ea.attribute_id',
            $eaColumns
        );

        // join additional attribute data table
        $additionalTable = $entityType->getAdditionalAttributeTable();
        if ($additionalTable) {
            $caDescribe = $connection->describeTable($this->getTable($additionalTable));
            unset($caDescribe['attribute_id']);
            foreach (array_keys($caDescribe) as $columnName) {
                $caColumns[$columnName] = $columnName;
            }

            $select->join(
                array('ca' => $this->getTable($additionalTable)),
                'main_table.attribute_id = ca.attribute_id',
                $caColumns
            );
        }

        // add scope values
        $saDescribe = $connection->describeTable($this->getTable('customer/eav_attribute_website'));
        unset($saDescribe['attribute_id']);
        foreach (array_keys($saDescribe) as $columnName) {
            if ($columnName == 'website_id') {
                $saColumns['scope_website_id'] = $columnName;
            } else {
                if (isset($eaColumns[$columnName])) {
                    $code = sprintf('scope_%s', $columnName);
                    $saColumns[$code] = new Zend_Db_Expr(sprintf('IFNULL(sa.%s, ea.%s)',
                        $columnName, $columnName));
                } else if (isset($caColumns[$columnName])) {
                    $code = sprintf('scope_%s', $columnName);
                    $saColumns[$code] = new Zend_Db_Expr(sprintf('IFNULL(sa.%s, ca.%s)',
                        $columnName, $columnName));
                }
            }
        }

        $store = $this->getStore();

        $select->joinLeft(
            array('sa' => $this->getTable('customer/eav_attribute_website')),
            'main_table.attribute_id = sa.attribute_id AND sa.website_id = :scope_website_id',
            $saColumns
        );
        $this->addBindParam('scope_website_id', (int)$store->getWebsiteId());

        // add store attribute label
        if ($store->isAdmin()) {
            $select->columns(array('store_label' => 'ea.frontend_label'));
        } else {
            $storeLabelExpr = $connection->getCheckSql('al.value IF NULL', 'ea.frontend_label', 'al.value');
            $select->joinLeft(
                array('al' => $this->getTable('eav/attribute_label')),
                'al.attribute_id = main_table.attribute_id AND al.store_id = :label_store_id',
                array('store_label' => $storeLabelExpr)
            );
            $this->addBindParam('label_store_id', (int)$store->getId());
        }

        $this->addBindParam('ea_entity_type_id', (int)$entityType->getId());
        // add entity type filter
        $select->where('ea.entity_type_id = :ea_entity_type_id');

        return parent::_beforeLoad();
    }
}
