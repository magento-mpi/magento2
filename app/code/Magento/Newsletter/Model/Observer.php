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
 * Newsletter module observer
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Newsletter_Model_Observer
{
    /**
     * Queue collection factory
     *
     * @var Magento_Newsletter_Model_Resource_Queue_CollectionFactory
     */
    protected $_queueCollectionFactory;

    /**
     * Subscriber factory
     *
     * @var Magento_Newsletter_Model_SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * Construct
     *
     * @param Magento_Newsletter_Model_SubscriberFactory $subscriberFactory
     * @param Magento_Newsletter_Model_Resource_Queue_CollectionFactory $queueCollectionFactory
     */
    public function __construct(
        Magento_Newsletter_Model_SubscriberFactory $subscriberFactory,
        Magento_Newsletter_Model_Resource_Queue_CollectionFactory $queueCollectionFactory
    ) {
        $this->_subscriberFactory = $subscriberFactory;
        $this->_queueCollectionFactory = $queueCollectionFactory;
    }

    public function subscribeCustomer($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if (($customer instanceof Magento_Customer_Model_Customer)) {
            $this->_subscriberFactory->create()->subscribeCustomer($customer);
        }
        return $this;
    }

    /**
     * Customer delete handler
     *
     * @param Magento_Object $observer
     * @return Magento_Newsletter_Model_Observer
     */
    public function customerDeleted($observer)
    {
        /** @var Magento_Newsletter_Model_Subscriber $subscriber */
        $subscriber = $this->_subscriberFactory->create();
        $subscriber->loadByEmail($observer->getEvent()->getCustomer()->getEmail());
        if($subscriber->getId()) {
            $subscriber->delete();
        }
        return $this;
    }

    public function scheduledSend($schedule)
    {
        $countOfQueue  = 3;
        $countOfSubscritions = 20;

        /** @var Magento_Newsletter_Model_Resource_Queue_Collection $collection */
        $collection = $this->_queueCollectionFactory->create();
        $collection->setPageSize($countOfQueue)
            ->setCurPage(1)
            ->addOnlyForSendingFilter()
            ->load();

         $collection->walk('sendPerSubscriber', array($countOfSubscritions));
    }
}
