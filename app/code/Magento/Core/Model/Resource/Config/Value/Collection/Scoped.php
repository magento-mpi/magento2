<?php
/**
 * Scoped config data collection
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_Resource_Config_Value_Collection_Scoped extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Scope to filter by
     *
     * @var string
     */
    protected $_scope;

    /**
     * Scope id to filter by
     *
     * @var int
     */
    protected $_scopeId;

    /**
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_Resource_Config_Data $resource
     * @param string $scope
     * @param int $scopeId
     */
    public function __construct(
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_Resource_Config_Data $resource,
        $scope,
        $scopeId = null
    ) {
        $this->_scope = $scope;
        $this->_scopeId = $scopeId;
        parent::__construct($fetchStrategy, $resource);
    }

    /**
     * Initialize select
     *
     * @return $this|Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToSelect(array('path', 'value'))
            ->addFieldToFilter('scope', $this->_scope);

        if ($this->_scopeId) {
            $this->addFieldToFilter('scope_id', $this->_scopeId);
        }
        return $this;
    }
}
