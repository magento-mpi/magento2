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
 * Layout update collection model
 */
class Mage_Core_Model_Resource_Layout_Update_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Name prefix of events that are dispatched by model
     *
     * @var string
     */
    protected $_eventPrefix = 'layout_update_collection';

    /**
     * Name of event parameter
     *
     * @var string
     */
    protected $_eventObject = 'layout_update_collection';

    /**
     * Name of layout update table
     *
     * @var string
     */
    protected $_layoutContextTable;

    /**
     * 'core_layout_update_context' table fields
     *
     * @var array
     */
    protected $_layoutContextFields = array(
        'entity_name',
        'entity_type',
        'value_varchar',
        'value_int',
        'value_datetime',
        'relation_hash',
        'relation_count'
    );

    /**
     * Define resource model
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Resource_Layout_Update');
        $this->_layoutContextTable = $this->getTable('core_layout_update_context');
    }

    /**
     * Add filter by context
     *
     * @param array $contextFilter
     * @return Mage_Core_Model_Resource_Layout_Update_Collection
     */
    public function addContextFilter($contextFilter)
    {
        foreach ($contextFilter as $conditionData) {
            $condition = array();
            $condition[] = $this->_translateCondition('entity_name', $conditionData['condition_entity_name']);
            $condition[] = $this->_translateCondition(
                'value_' . $conditionData['entity_type'],
                $conditionData['condition_entity_value']
            );
            $this->addFilter('context_filter', implode(' ' . Zend_Db_Select::SQL_AND . ' ', $condition),
                'or_string');
        }
        return $this;
    }

    /**
     * Join context relation table if there is context filter
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('context_filter')) {
            $this->joinLayoutContext();
            $this->_useAnalyticFunction = true;
        }
        return parent::_renderFiltersBefore();
    }

    /**
     * Map 'core_layout_update_context' table fields
     *
     * @throws Magento_Exception
     * @return Mage_Core_Model_Resource_Layout_Update_Collection
     */
    protected function _mapLayoutContextFields()
    {
        if (!array_walk($this->_layoutContextFields, array($this, '_mapContextField'))) {
            throw new Magento_Exception('Can\'t map context fields');
        }
        return $this;
    }


    /**
     * Map layout context field
     *
     * @param string $field
     * @return Varien_Data_Collection_Db
     */
    protected function _mapContextField($field)
    {
        return $this->addFilterToMap($field, $this->_layoutContextTable . '.' . $field);
    }

    /**
     * Join 'core_layout_update_context' table
     *
     * @return Mage_Core_Model_Resource_Layout_Update_Collection
     */
    public function joinLayoutContext()
    {
        $this->_fieldsToSelect = array();
        $this->_initialFieldsToSelect = array();
        $this->_mapLayoutContextFields()->removeFieldFromSelect('*')
            ->addFieldToSelect(array(
                'layout_update' => new Zend_Db_Expr('MAX(main_table.xml)'),
            ))
            ->getSelect()
            ->joinInner(
                $this->_layoutContextTable,
                'main_table.layout_update_id = ' . $this->_layoutContextTable . '.layout_update_id',
                array($this->_getMappedField('relation_hash')))
            ->group($this->_getMappedField('relation_hash'))
            ->having('COUNT(*)=AVG(' . $this->_getMappedField('relation_count') . ')');

        return $this;
    }

    /**
     * Get layout update array
     *
     * @return array
     */
    public function toLayoutUpdateArray()
    {
        $result = array();
        foreach ($this as $item) {
            $result[] = $item->getData('layout_update');
        }
        return $result;
    }
}
