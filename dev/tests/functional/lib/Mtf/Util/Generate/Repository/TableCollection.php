<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Generate\Repository;

use Magento\Core\Model\Resource\Db\Collection\AbstractCollection;

/**
 * Class CollectionProvider
 *
 * @package Mtf\Util\Generate\Repository
 */
class TableCollection extends AbstractCollection
{
    /**
     * @var array
     */
    protected $fixture;

    /**
     * @constructor
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param null $connection
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     * @param array $fixture
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        $connection = null,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null,
        array $fixture = []
    ) {
        $this->setModel('Magento\Object');
        $this->setResourceModel('Mtf\Util\Generate\Repository\Resource');

        $resource = $this->getResource();
        $resource->setFixture($fixture);

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Get resource instance
     *
     * @return \Mtf\Util\Generate\Repository\Resource
     */
    public function getResource()
    {
        return parent::getResource();
    }
}
