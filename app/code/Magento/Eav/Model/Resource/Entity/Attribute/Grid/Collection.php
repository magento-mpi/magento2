<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Eav Resource Attribute Set Collection
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Resource\Entity\Attribute\Grid;

class Collection
    extends \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection
{
    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registryManager;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Core\Model\Registry $registryManager
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Core\Model\Registry $registryManager,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_registryManager = $registryManager;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
    }

    /**
     *  Add filter by entity type id to collection
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection|\Magento\Eav\Model\Resource\Entity\Attribute\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->setEntityTypeFilter($this->_registryManager->registry('entityType'));
        return $this;
    }
}
