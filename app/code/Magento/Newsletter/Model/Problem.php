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
 * Newsletter problem model
 *
 * @method Magento_Newsletter_Model_Resource_Problem _getResource()
 * @method Magento_Newsletter_Model_Resource_Problem getResource()
 * @method int getSubscriberId()
 * @method Magento_Newsletter_Model_Problem setSubscriberId(int $value)
 * @method int getQueueId()
 * @method Magento_Newsletter_Model_Problem setQueueId(int $value)
 * @method int getProblemErrorCode()
 * @method Magento_Newsletter_Model_Problem setProblemErrorCode(int $value)
 * @method string getProblemErrorText()
 * @method Magento_Newsletter_Model_Problem setProblemErrorText(string $value)
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Newsletter_Model_Problem extends Magento_Core_Model_Abstract
{
    /**
     * Current Subscriber
     * 
     * @var Magento_Newsletter_Model_Subscriber
     */
    protected  $_subscriber = null;

    /**
     * Initialize Newsletter Problem Model
     */
    protected function _construct()
    {
        $this->_init('Magento_Newsletter_Model_Resource_Problem');
    }

    /**
     * Add Subscriber Data
     *
     * @param Magento_Newsletter_Model_Subscriber $subscriber
     * @return Magento_Newsletter_Model_Problem
     */
    public function addSubscriberData(Magento_Newsletter_Model_Subscriber $subscriber)
    {
        $this->setSubscriberId($subscriber->getId());
        return $this;
    }

    /**
     * Add Queue Data
     *
     * @param Magento_Newsletter_Model_Queue $queue
     * @return Magento_Newsletter_Model_Problem
     */
    public function addQueueData(Magento_Newsletter_Model_Queue $queue)
    {
        $this->setQueueId($queue->getId());
        return $this;
    }

    /**
     * Add Error Data
     *
     * @param Exception $e
     * @return Magento_Newsletter_Model_Problem
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
     * @return Magento_Newsletter_Model_Subscriber
     */
    public function getSubscriber()
    {
        if(!$this->getSubscriberId()) {
            return null;
        }

        if(is_null($this->_subscriber)) {
            $this->_subscriber = Mage::getModel('Magento_Newsletter_Model_Subscriber')
                ->load($this->getSubscriberId());
        }

        return $this->_subscriber;
    }

    /**
     * Unsubscribe Subscriber
     *
     * @return Magento_Newsletter_Model_Problem
     */
    public function unsubscribe()
    {
        if($this->getSubscriber()) {
            $this->getSubscriber()->setSubscriberStatus(Magento_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED)
                ->setIsStatusChanged(true)
                ->save();
        }
        return $this;
    }

}
