<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core Design resource collection
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Resource\Design;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->dateTime = $dateTime;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
    }

    /**
     * Core Design resource collection
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
         return $this->join(
            array('cs' => 'core_store'),
            'cs.store_id = main_table.store_id',
            array('cs.name'));
    }

    /**
     * Add date filter to collection
     *
     * @param null|int|string|Zend_Date $date
     * @return \Magento\Core\Model\Resource\Design\Collection
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
