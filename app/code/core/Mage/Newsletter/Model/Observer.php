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
 * Newsletter module observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_Model_Observer
{
    public function subscribeCustomer($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if (($customer instanceof Mage_Customer_Model_Customer)) {
            Mage::getModel('Mage_Newsletter_Model_Subscriber')->subscribeCustomer($customer);
        }
        return $this;
    }

    /**
     * Customer delete handler
     *
     * @param Varien_Object $observer
     * @return Mage_Newsletter_Model_Observer
     */
    public function customerDeleted($observer)
    {
        $subscriber = Mage::getModel('Mage_Newsletter_Model_Subscriber')
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

        $collection = Mage::getModel('Mage_Newsletter_Model_Queue')->getCollection()
            ->setPageSize($countOfQueue)
            ->setCurPage(1)
            ->addOnlyForSendingFilter()
            ->load();

         $collection->walk('sendPerSubscriber', array($countOfSubscritions));
    }

    /**
     * Set the subscriber store id on the transport object
     *
     * Set the subscriber store id on the transport object so the logo configuration for the
     * subscriber store is read (which is not necessarily the current one).
     *
     * @param Varien_Event_Observer $observer
     */
    public function emailTemplateFilterBefore($observer)
    {
        $transport = $observer->getEvent()->getTransport();
        $variables = $transport->getVariables();
        // only match newsletter subscriber events
        if (!empty($variables['subscriber']) && $variables['subscriber'] instanceof Mage_Newsletter_Model_Subscriber) {
            $transport->setStoreId($variables['subscriber']->getStoreId());
        }
    }
}
