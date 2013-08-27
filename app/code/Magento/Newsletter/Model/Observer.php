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
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Newsletter_Model_Observer
{
    public function subscribeCustomer($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if (($customer instanceof Magento_Customer_Model_Customer)) {
            Mage::getModel('Magento_Newsletter_Model_Subscriber')->subscribeCustomer($customer);
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
        $subscriber = Mage::getModel('Magento_Newsletter_Model_Subscriber')
            ->loadByEmail($observer->getEvent()->getCustomer()->getEmail());
        if($subscriber->getId()) {
            $subscriber->delete();
        }
        return $this;
    }

    public function scheduledSend($schedule)
    {
        $countOfQueue  = 3;
        $countOfSubscritions = 20;

        $collection = Mage::getModel('Magento_Newsletter_Model_Queue')->getCollection()
            ->setPageSize($countOfQueue)
            ->setCurPage(1)
            ->addOnlyForSendingFilter()
            ->load();

         $collection->walk('sendPerSubscriber', array($countOfSubscritions));
    }
}
