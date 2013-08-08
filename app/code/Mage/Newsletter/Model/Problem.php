<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter problem model
 *
 * @method Mage_Newsletter_Model_Resource_Problem _getResource()
 * @method Mage_Newsletter_Model_Resource_Problem getResource()
 * @method int getSubscriberId()
 * @method Mage_Newsletter_Model_Problem setSubscriberId(int $value)
 * @method int getQueueId()
 * @method Mage_Newsletter_Model_Problem setQueueId(int $value)
 * @method int getProblemErrorCode()
 * @method Mage_Newsletter_Model_Problem setProblemErrorCode(int $value)
 * @method string getProblemErrorText()
 * @method Mage_Newsletter_Model_Problem setProblemErrorText(string $value)
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_Model_Problem extends Magento_Core_Model_Abstract
{
    /**
     * Current Subscriber
     * 
     * @var Mage_Newsletter_Model_Subscriber
     */
    protected  $_subscriber = null;

    /**
     * Initialize Newsletter Problem Model
     */
    protected function _construct()
    {
        $this->_init('Mage_Newsletter_Model_Resource_Problem');
    }

    /**
     * Add Subscriber Data
     *
     * @param Mage_Newsletter_Model_Subscriber $subscriber
     * @return Mage_Newsletter_Model_Problem
     */
    public function addSubscriberData(Mage_Newsletter_Model_Subscriber $subscriber)
    {
        $this->setSubscriberId($subscriber->getId());
        return $this;
    }

    /**
     * Add Queue Data
     *
     * @param Mage_Newsletter_Model_Queue $queue
     * @return Mage_Newsletter_Model_Problem
     */
    public function addQueueData(Mage_Newsletter_Model_Queue $queue)
    {
        $this->setQueueId($queue->getId());
        return $this;
    }

    /**
     * Add Error Data
     *
     * @param Exception $e
     * @return Mage_Newsletter_Model_Problem
     */
    public function addErrorData(Exception $e)
    {
        $this->setProblemErrorCode($e->getCode());
        $this->setProblemErrorText($e->getMessage());
        return $this;
    }

    /**
     * Retrieve Subscriber
     *
     * @return Mage_Newsletter_Model_Subscriber
     */
    public function getSubscriber()
    {
        if(!$this->getSubscriberId()) {
            return null;
        }

        if(is_null($this->_subscriber)) {
            $this->_subscriber = Mage::getModel('Mage_Newsletter_Model_Subscriber')
                ->load($this->getSubscriberId());
        }

        return $this->_subscriber;
    }

    /**
     * Unsubscribe Subscriber
     *
     * @return Mage_Newsletter_Model_Problem
     */
    public function unsubscribe()
    {
        if($this->getSubscriber()) {
            $this->getSubscriber()->setSubscriberStatus(Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED)
                ->setIsStatusChanged(true)
                ->save();
        }
        return $this;
    }

}
