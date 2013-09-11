<?php
/**
 * Scoped config data collection
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Resource\Config\Value\Collection;

class Scoped extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
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
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\Resource\Config\Data $resource
     * @param string $scope
     * @param int $scopeId
     */
    public function __construct(
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\Resource\Config\Data $resource,
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
     * @return $this|\Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToSelect(array('path', 'value'))
            ->addFieldToFilter('scope', $this->_scope);

        if (!is_null($this->_scopeId)) {
            $this->addFieldToFilter('scope_id', $this->_scopeId);
        }
        return $this;
    }
}
