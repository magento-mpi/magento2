<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Model;

/**
 * Newsletter module observer
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Observer
{
    /**
     * Queue collection factory
     *
     * @var \Magento\Newsletter\Model\Resource\Queue\CollectionFactory
     */
    protected $_queueCollectionFactory;

    /**
     * Subscriber factory
     *
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * Construct
     *
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Newsletter\Model\Resource\Queue\CollectionFactory $queueCollectionFactory
     */
    public function __construct(
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Newsletter\Model\Resource\Queue\CollectionFactory $queueCollectionFactory
    ) {
        $this->_subscriberFactory = $subscriberFactory;
        $this->_queueCollectionFactory = $queueCollectionFactory;
    }

    public function subscribeCustomer($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if (($customer instanceof \Magento\Customer\Model\Customer)) {
            $this->_subscriberFactory->create()->subscribeCustomer($customer);
        }
        return $this;
    }

    /**
     * Customer delete handler
     *
     * @param \Magento\Object $observer
     * @return \Magento\Newsletter\Model\Observer
     */
    public function customerDeleted($observer)
    {
        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
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

        /** @var \Magento\Newsletter\Model\Resource\Queue\Collection $collection */
        $collection = $this->_queueCollectionFactory->create();
        $collection->setPageSize($countOfQueue)
            ->setCurPage(1)
            ->addOnlyForSendingFilter()
            ->load();

         $collection->walk('sendPerSubscriber', array($countOfSubscritions));
    }
}
