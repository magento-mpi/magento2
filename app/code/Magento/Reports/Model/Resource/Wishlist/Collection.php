<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist Report collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model\Resource\Wishlist;

class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Wishlist table name
     *
     * @var string
     */
    protected $_wishlistTable;

    /**
     * @var \Magento\Customer\Model\Resource\Customer\CollectionFactory
     */
    protected $_customerResFactory;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Customer\Model\Resource\Customer\CollectionFactory $customerResFactory
     * @param mixed $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Customer\Model\Resource\Customer\CollectionFactory $customerResFactory,
        $connection = null,
        \Magento\Framework\Model\Resource\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_customerResFactory = $customerResFactory;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Wishlist\Model\Wishlist', 'Magento\Wishlist\Model\Resource\Wishlist');
        $this->setWishlistTable($this->getTable('wishlist'));
    }

    /**
     * Set wishlist table name
     *
     * @param string $value
     * @return $this
     */
    public function setWishlistTable($value)
    {
        $this->_wishlistTable = $value;
        return $this;
    }

    /**
     * Retrieve wishlist table name
     *
     * @return string
     */
    public function getWishlistTable()
    {
        return $this->_wishlistTable;
    }

    /**
     * Retrieve wishlist customer count
     *
     * @return array
     */
    public function getWishlistCustomerCount()
    {
        /** @var $collection \Magento\Customer\Model\Resource\Customer\Collection */
        $collection = $this->_customerResFactory->create();

        $customersSelect = $collection->getSelectCountSql();

        $countSelect = clone $customersSelect;
        $countSelect->joinLeft(
            array('wt' => $this->getWishlistTable()),
            'wt.customer_id = e.entity_id',
            array()
        )->group(
            'wt.wishlist_id'
        );
        $count = $collection->count();
        $resultSelect = $this->getConnection()->select()->union(
            array($customersSelect, $count),
            \Zend_Db_Select::SQL_UNION_ALL
        );
        list($customers, $count) = $this->getConnection()->fetchCol($resultSelect);

        return array($count * 100 / $customers, $count);
    }

    /**
     * Get shared items collection count
     *
     * @return int
     */
    public function getSharedCount()
    {
        /** @var $collection \Magento\Customer\Model\Resource\Customer\Collection */
        $collection = $this->_customerResFactory->create();
        $countSelect = $collection->getSelectCountSql();
        $countSelect->joinLeft(
            array('wt' => $this->getWishlistTable()),
            'wt.customer_id = e.entity_id',
            array()
        )->where(
            'wt.shared > 0'
        )->group(
            'wt.wishlist_id'
        );
        return $countSelect->getAdapter()->fetchOne($countSelect);
    }
}
