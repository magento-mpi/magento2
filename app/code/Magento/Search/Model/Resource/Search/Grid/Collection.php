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
     * @var \Magento\Core\Model\Registry
     */
    protected $_registryManager;

    /**
     * Construct
     *
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\CatalogSearch\Model\Resource\Helper\Mysql4 $resourceHelper
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogSearch\Model\Resource\Helper\Mysql4 $resourceHelper,
        \Magento\Core\Model\Registry $registry,
        $resource = null
    ) {
        $this->_registryManager = $registry;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $storeManager, $resourceHelper,
            $resource);
    }

    /**
     * @return \Magento\Search\Model\Resource\Search\Grid\Collection
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
