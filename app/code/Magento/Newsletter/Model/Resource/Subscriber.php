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
class Magento_Newsletter_Model_Resource_Subscriber extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * DB read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * DB write connection
     *
     * @var Zend_Db_Adapter_Abstract
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
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Class constructor
     *
     *
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Resource $resource
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Resource $resource
    ) {
        $this->_coreData = $coreData;
        parent::__construct($resource);
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
     * @param Magento_Customer_Model_Customer $customer
     * @return array
     */
    public function loadByCustomer(Magento_Customer_Model_Customer $customer)
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
        return $this->_coreData->uniqHash();
    }

    /**
     * Updates data when subscriber received
     *
     * @param Magento_Newsletter_Model_Subscriber $subscriber
     * @param Magento_Newsletter_Model_Queue $queue
     * @return Magento_Newsletter_Model_Resource_Subscriber
     */
    public function received(Magento_Newsletter_Model_Subscriber $subscriber, Magento_Newsletter_Model_Queue $queue)
    {
        $this->_write->beginTransaction();
        try {
            $data['letter_sent_at'] = Mage::getSingleton('Magento_Core_Model_Date')->gmtDate();
            $this->_write->update($this->_subscriberLinkTable, $data, array(
                'subscriber_id = ?' => $subscriber->getId(),
                'queue_id = ?' => $queue->getId()
            ));
            $this->_write->commit();
        }
        catch (Exception $e) {
            $this->_write->rollBack();
            Mage::throwException(__('We cannot mark as received subscriber.'));
        }
        return $this;
    }
}
