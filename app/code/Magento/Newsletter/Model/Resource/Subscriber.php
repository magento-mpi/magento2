<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Newsletter subscriber resource model
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Model\Resource;

class Subscriber extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * DB read connection
     *
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * DB write connection
     *
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $_write;

    /**
     * Name of subscriber link DB table
     *
     * @var string
     */
    protected $_subscriberLinkTable;

    /**
     * Name of scope for error messages
     *
     * @var string
     */
    protected $_messagesScope          = 'newsletter/session';

    /**
     * Date
     *
     * @var \Magento\Core\Model\Date
     */
    protected $_date;

    /**
     * Construct
     *
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Core\Model\Date $date
     * @internal param \Magento\Core\Helper\Data $coreData
     */
    public function __construct(
        \Magento\Core\Model\Resource $resource,
        \Magento\Core\Model\Date $date
    ) {
        parent::__construct($resource);
        $this->_date = $date;
    }

    /**
     * Initialize resource model
     * Get tablename from config
     *
     */
    protected function _construct()
    {
        $this->_init('newsletter_subscriber', 'subscriber_id');
        $this->_subscriberLinkTable = $this->getTable('newsletter_queue_link');
        $this->_read = $this->_getReadAdapter();
        $this->_write = $this->_getWriteAdapter();
    }

    /**
     * Set error messages scope
     *
     * @param string $scope
     */
    public function setMessagesScope($scope)
    {
        $this->_messagesScope = $scope;
    }

    /**
     * Load subscriber from DB by email
     *
     * @param string $subscriberEmail
     * @return array
     */
    public function loadByEmail($subscriberEmail)
    {
        $select = $this->_read->select()
            ->from($this->getMainTable())
            ->where('subscriber_email=:subscriber_email');

        $result = $this->_read->fetchRow($select, array('subscriber_email'=>$subscriberEmail));

        if (!$result) {
            return array();
        }

        return $result;
    }

    /**
     * Load subscriber by customer
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return array
     */
    public function loadByCustomer(\Magento\Customer\Model\Customer $customer)
    {
        $select = $this->_read->select()
            ->from($this->getMainTable())
            ->where('customer_id=:customer_id');

        $result = $this->_read->fetchRow($select, array('customer_id'=>$customer->getId()));

        if ($result) {
            return $result;
        }

        $select = $this->_read->select()
            ->from($this->getMainTable())
            ->where('subscriber_email=:subscriber_email');

        $result = $this->_read->fetchRow($select, array('subscriber_email'=>$customer->getEmail()));

        if ($result) {
            return $result;
        }

        return array();
    }

    /**
     * Generates random code for subscription confirmation
     *
     * @return string
     */
    protected function _generateRandomCode()
    {
        return \Magento\Math\Random::getUniqueHash();
    }

    /**
     * Updates data when subscriber received
     *
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param \Magento\Newsletter\Model\Queue $queue
     * @return \Magento\Newsletter\Model\Resource\Subscriber
     * @throws \Magento\Core\Exception
     */
    public function received(\Magento\Newsletter\Model\Subscriber $subscriber, \Magento\Newsletter\Model\Queue $queue)
    {
        $this->_write->beginTransaction();
        try {
            $data['letter_sent_at'] = $this->_date->gmtDate();
            $this->_write->update($this->_subscriberLinkTable, $data, array(
                'subscriber_id = ?' => $subscriber->getId(),
                'queue_id = ?' => $queue->getId()
            ));
            $this->_write->commit();
        }
        catch (\Exception $e) {
            $this->_write->rollBack();
            throw new \Magento\Core\Exception(__('We cannot mark as received subscriber.'));
        }
        return $this;
    }
}
