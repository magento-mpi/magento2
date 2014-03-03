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
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\Resource\Config\Data $resource
     * @param string $scope
     * @param mixed $connection
     * @param mixed $scopeId
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\Resource\Config\Data $resource,
        $scope,
        $connection = null,
        $scopeId = null
    ) {
        $this->_scope = $scope;
        $this->_scopeId = $scopeId;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Initialize select
     *
     * @return $this
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
