<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource\Design;

/**
 * Core Design resource collection
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param mixed $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $connection = null,
        \Magento\Framework\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->dateTime = $dateTime;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Core Design resource collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Design', 'Magento\Core\Model\Resource\Design');
    }

    /**
     * Join store data to collection
     *
     * @return \Magento\Core\Model\Resource\Design\Collection
     */
    public function joinStore()
    {
        return $this->join(array('cs' => 'store'), 'cs.store_id = main_table.store_id', array('cs.name'));
    }

    /**
     * Add date filter to collection
     *
     * @param null|int|string|\Zend_Date $date
     * @return $this
     */
    public function addDateFilter($date = null)
    {
        if (is_null($date)) {
            $date = $this->dateTime->formatDate(true);
        } else {
            $date = $this->dateTime->formatDate($date);
        }

        $this->addFieldToFilter('date_from', array('lteq' => $date));
        $this->addFieldToFilter('date_to', array('gteq' => $date));
        return $this;
    }

    /**
     * Add store filter to collection
     *
     * @param int|array $storeId
     * @return \Magento\Core\Model\Resource\Design\Collection
     */
    public function addStoreFilter($storeId)
    {
        return $this->addFieldToFilter('store_id', array('in' => $storeId));
    }
}
