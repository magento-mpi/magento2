<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Custom variabel collection
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Variable_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Store Id
     *
     * @var int
     */
    protected $_storeId    = 0;

    /**
     *  Define resource model
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Mage_Core_Model_Variable', 'Mage_Core_Model_Resource_Variable');
    }

    /**
     * Setter
     *
     * @param integer $storeId
     * @return Mage_Core_Model_Resource_Variable_Collection
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Add store values to result
     *
     * @return Mage_Core_Model_Resource_Variable_Collection
     */
    public function addValuesToResult()
    {
        $this->getSelect()
            ->join(
                array('value_table' => $this->getTable('core_variable_value')),
                'value_table.variable_id = main_table.variable_id',
                array('value_table.value'));
        $this->addFieldToFilter('value_table.store_id', array('eq' => $this->getStoreId()));
        return $this;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('code', 'name');
    }
}