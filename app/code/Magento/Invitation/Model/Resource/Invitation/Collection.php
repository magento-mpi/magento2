<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Invitation\Model\Resource\Invitation;

/**
 * Invitation collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Invitation\Model\Invitation\Status
     */
    protected $status;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Invitation\Model\Invitation\Status $status
     * @param \Zend_Db_Adapter_Abstract $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Invitation\Model\Invitation\Status $status,
        $connection = null,
        \Magento\Framework\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->status = $status;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Fields mapping
     *
     * @var array
     */
    protected $_map = [
        'fields' => [
            'invitee_email' => 'c.email',
            'website_id' => 'w.website_id',
            'invitation_email' => 'main_table.email',
            'invitee_group_id' => 'main_table.group_id',
        ],
    ];

    /**
     * Intialize collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Invitation\Model\Invitation', 'Magento\Invitation\Model\Resource\Invitation');
    }

    /**
     * Instantiate select object
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(
            ['main_table' => $this->getResource()->getMainTable()],
            ['*', 'invitation_email' => 'email', 'invitee_group_id' => 'group_id']
        );
        return $this;
    }

    /**
     * Load collection where customer id equals passed parameter
     *
     * @param int $id
     * @return $this
     */
    public function loadByCustomerId($id)
    {
        $this->getSelect()->where('main_table.customer_id = ?', $id);
        return $this->load();
    }

    /**
     * Filter by specified store ids
     *
     * @param int[]|int $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }

    /**
     * Join website ID
     *
     * @return $this
     */
    public function addWebsiteInformation()
    {
        $this->getSelect()->joinInner(
            ['w' => $this->getTable('store')],
            'main_table.store_id = w.store_id',
            'w.website_id'
        );
        return $this;
    }

    /**
     * Join referrals information (email)
     *
     * @return $this
     */
    public function addInviteeInformation()
    {
        $this->getSelect()->joinLeft(
            ['c' => $this->getTable('customer_entity')],
            'main_table.referral_id = c.entity_id',
            ['invitee_email' => 'c.email']
        );
        return $this;
    }

    /**
     * Filter collection by items that can be sent
     *
     * @return $this
     */
    public function addCanBeSentFilter()
    {
        return $this->addFieldToFilter('status', ["in" => $this->status->getCanBeSentStatuses()]);
    }

    /**
     * Filter collection by items that can be cancelled
     *
     * @return $this
     */
    public function addCanBeCanceledFilter()
    {
        return $this->addFieldToFilter('status', ["in" => $this->status->getCanBeCancelledStatuses()]);
    }
}
