<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Resource\Search\Grid;

class Collection
    extends \Magento\CatalogSearch\Model\Resource\Query\Collection
{
    /**
     * Registry manager
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registryManager;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\CatalogSearch\Model\Resource\Helper $resourceHelper
     * @param \Magento\Core\Model\Registry $registry
     * @param mixed $connection
     * @param mixed $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogSearch\Model\Resource\Helper $resourceHelper,
        \Magento\Core\Model\Registry $registry,
        $connection = null,
        $resource = null
    ) {
        $this->_registryManager = $registry;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $resourceHelper,
            $connection,
            $resource
        );
    }

    /**
     * Initialize select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $queryId = $this->getQuery()->getId();
        if ($queryId) {
            $this->addFieldToFilter('query_id', array('nin' => $queryId));
        }
        return $this;
    }

    /**
     *  Retrieve a value from registry by a key
     *
     * @return \Magento\CatalogSearch\Model\Query
     */
    public function getQuery()
    {
        return $this->_registryManager->registry('current_catalog_search');
    }
}
