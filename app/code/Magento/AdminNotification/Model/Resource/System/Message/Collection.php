<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\AdminNotification\Model\Resource\System\Message;

class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * System message list
     *
     * @var \Magento\AdminNotification\Model\System\MessageList
     */
    protected $_messageList;

    /**
     * Number of messages by severity
     *
     * @var array
     */
    protected $_countBySeverity = array();

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\AdminNotification\Model\System\MessageList $messageList
     * @param mixed $connection
     * @param \Magento\Framework\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\AdminNotification\Model\System\MessageList $messageList,
        $connection = null,
        \Magento\Framework\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_messageList = $messageList;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Resource collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\AdminNotification\Model\System\Message',
            'Magento\AdminNotification\Model\Resource\System\Message'
        );
    }

    /**
     * Initialize db query
     *
     * @return void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addOrder('severity', self::SORT_ORDER_ASC)->addOrder('created_at');
    }

    /**
     * Initialize system messages after load
     *
     * @return void
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $key => $item) {
            $message = $this->_messageList->getMessageByIdentity($item->getIdentity());
            if ($message) {
                $item->setText($message->getText());
                if (array_key_exists($message->getSeverity(), $this->_countBySeverity)) {
                    $this->_countBySeverity[$message->getSeverity()]++;
                } else {
                    $this->_countBySeverity[$message->getSeverity()] = 1;
                }
            } else {
                unset($this->_items[$key]);
            }
        }
    }

    /**
     * Set message severity filter
     *
     * @param int $severity
     * @return $this
     */
    public function setSeverity($severity)
    {
        $this->addFieldToFilter('severity', array('eq' => $severity * 1));
        return $this;
    }

    /**
     * Retrieve number of messages by severity
     *
     * @param int $severity
     * @return int
     */
    public function getCountBySeverity($severity)
    {
        return isset($this->_countBySeverity[$severity]) ? $this->_countBySeverity[$severity] : 0;
    }
}
