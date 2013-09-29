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
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Core\Model\Resource\Config\Data $resource
     * @param $scope
     * @param null $scopeId
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Core\Model\Resource\Config\Data $resource,
        $scope,
        $scopeId = null
    ) {
        $this->_scope = $scope;
        $this->_scopeId = $scopeId;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
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
