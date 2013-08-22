<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Custom variable resource model
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Resource_Variable extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        $this->_init('core_variable', 'variable_id');
    }

    /**
     * Load variable by code
     *
     * @param Magento_Core_Model_Variable $object
     * @param string $code
     * @return Magento_Core_Model_Resource_Variable
     */
    public function loadByCode(Magento_Core_Model_Variable $object, $code)
    {
        if ($result = $this->getVariableByCode($code, true, $object->getStoreId())) {
            $object->setData($result);
        }
        return $this;
    }

    /**
     * Retrieve variable data by code
     *
     * @param string $code
     * @param boolean $withValue
     * @param integer $storeId
     * @return array
     */
    public function getVariableByCode($code, $withValue = false, $storeId = 0)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where($this->getMainTable() . '.code = ?', $code);
        if ($withValue) {
            $this->_addValueToSelect($select, $storeId);
        }
        return $this->_getReadAdapter()->fetchRow($select);
    }

    /**
     * Perform actions after object save
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Core_Model_Resource_Variable
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        parent::_afterSave($object);
        if ($object->getUseDefaultValue()) {
            /*
             * remove store value
             */
            $this->_getWriteAdapter()->delete(
                $this->getTable('core_variable_value'), array(
                    'variable_id = ?' => $object->getId(),
                    'store_id = ?' => $object->getStoreId()
            ));
        } else {
            $data =  array(
                'variable_id' => $object->getId(),
                'store_id'    => $object->getStoreId(),
                'plain_value' => $object->getPlainValue(),
                'html_value'  => $object->getHtmlValue()
            );
            $data = $this->_prepareDataForTable(new Magento_Object($data), $this->getTable('core_variable_value'));
            $this->_getWriteAdapter()->insertOnDuplicate(
                $this->getTable('core_variable_value'),
                $data,
                array('plain_value', 'html_value')
            );
        }
        return $this;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Magento_Core_Model_Abstract $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $this->_addValueToSelect($select, $object->getStoreId());
        return $select;
    }

    /**
     * Add variable store and default value to select
     *
     * @param Zend_Db_Select $select
     * @param integer $storeId
     * @return Magento_Core_Model_Resource_Variable
     */
    protected function _addValueToSelect(Zend_Db_Select $select, $storeId = Magento_Core_Model_AppInterface::ADMIN_STORE_ID)
    {
        $adapter = $this->_getReadAdapter();
        $ifNullPlainValue = $adapter->getCheckSql('store.plain_value IS NULL', 'def.plain_value', 'store.plain_value');
        $ifNullHtmlValue  = $adapter->getCheckSql('store.html_value IS NULL', 'def.html_value', 'store.html_value');

        $select->joinLeft(
                array('def' => $this->getTable('core_variable_value')),
                'def.variable_id = '.$this->getMainTable().'.variable_id AND def.store_id = 0',
                array())
            ->joinLeft(
                array('store' => $this->getTable('core_variable_value')),
                'store.variable_id = def.variable_id AND store.store_id = ' . $adapter->quote($storeId),
                array())
            ->columns(array(
                'plain_value'       => $ifNullPlainValue,
                'html_value'        => $ifNullHtmlValue,
                'store_plain_value' => 'store.plain_value',
                'store_html_value'  => 'store.html_value'
            ));

        return $this;
    }
}
