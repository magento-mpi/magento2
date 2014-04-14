<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Model\Resource\Problem;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Exception\NoSuchEntityException;

/**
 * Newsletter problems collection
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Collection extends \Magento\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * True when subscribers info joined
     *
     * @var bool
     */
    protected $_subscribersInfoJoinedFlag = false;

    /**
     * True when grouped
     *
     * @var bool
     */
    protected $_problemGrouped = false;

    /**
     * Customer collection factory
     *
     * @var \Magento\Customer\Model\Resource\Customer\CollectionFactory
     */
    protected $_customerCollectionFactory;

    /**
     * Customer Service
     *
     * @var CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * Customer View Helper
     *
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerView;

    /**
     * checks if customer data is loaded
     *
     * @var boolean
     */
    protected $_loadCustomersDataFlag = false;


    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param CustomerAccountServiceInterface $customerAccountService,
     * @param \Magento\Customer\Helper\View $customerView
     * @param null|\Zend_Db_Adapter_Abstract $connection
     * @param \Magento\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        CustomerAccountServiceInterface $customerAccountService,
        \Magento\Customer\Helper\View $customerView,
        $connection = null,
        \Magento\Model\Resource\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_customerAccountService = $customerAccountService;
        $this->_customerView = $customerView;
    }

    /**
     * Define resource model and model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Newsletter\Model\Problem', 'Magento\Newsletter\Model\Resource\Problem');
    }

    /**
     * Set customer loaded status
     *
     * @param bool $flag
     * @return $this
     */
    protected function _setIsLoaded($flag = true)
    {
        if (!$flag) {
            $this->_loadCustomersDataFlag = false;
        }
        return parent::_setIsLoaded($flag);
    }
    /**
     * Adds subscribers info
     *
     * @return $this
     */
    public function addSubscriberInfo()
    {
        $this->getSelect()->joinLeft(
            array('subscriber' => $this->getTable('newsletter_subscriber')),
            'main_table.subscriber_id = subscriber.subscriber_id',
            array('subscriber_email', 'customer_id', 'subscriber_status')
        );
        $this->addFilterToMap('subscriber_id', 'main_table.subscriber_id');
        $this->_subscribersInfoJoinedFlag = true;

        return $this;
    }

    /**
     * Adds queue info
     *
     * @return $this
     */
    public function addQueueInfo()
    {
        $this->getSelect()->joinLeft(
            array('queue' => $this->getTable('newsletter_queue')),
            'main_table.queue_id = queue.queue_id',
            array('queue_start_at', 'queue_finish_at')
        )->joinLeft(
            array('template' => $this->getTable('newsletter_template')),
            'queue.template_id = template.template_id',
            array('template_subject', 'template_code', 'template_sender_name', 'template_sender_email')
        );
        return $this;
    }

    /**
     * Loads customers info to collection
     *
     * @return void
     */
    protected function _addCustomersData()
    {
        if ($this->_loadCustomersDataFlag) {
            return;
        }
        $this->_loadCustomersDataFlag = true;
        foreach ($this->getItems() as $item) {
            if ($item->getCustomerId()) {
                $customerId = $item->getCustomerId();
                try {
                    $customer = $this->_customerAccountService->getCustomer($customerId);
                    $problems = $this->getItemsByColumnValue('customer_id', $customerId);
                    $customerName = $this->_customerView->getCustomerName($customer);
                    foreach ($problems as $problem) {
                        $problem->setCustomerName($customerName)
                            ->setCustomerFirstName($customer->getFirstName())
                            ->setCustomerLastName($customer->getLastName());
                    }
                } catch (NoSuchEntityException $e) {
                    // do nothing if customer is not found by id
                }
            }
        }
    }

    /**
     * Loads collecion and adds customers info
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        parent::load($printQuery, $logQuery);
        if ($this->_subscribersInfoJoinedFlag) {
            $this->_addCustomersData();
        }
        return $this;
    }
}
