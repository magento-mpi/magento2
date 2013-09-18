<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating grid collection
 *
 * @category    Magento
 * @package     Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rating\Model\Resource\Rating\Grid;

class Collection extends \Magento\Rating\Model\Resource\Rating\Collection
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Collection constructor
     *
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null,
        $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($eventManager, $fetchStrategy, $resource, $data);
    }

    /**
     * Add entity filter
     *
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection|\Magento\Rating\Model\Resource\Rating\Grid\Collection
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addEntityFilter($this->_coreRegistry->registry('entityId'));
        return $this;
    }
}
