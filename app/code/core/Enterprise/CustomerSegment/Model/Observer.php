<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CustomerSegment observer
 *
 */
class Enterprise_CustomerSegment_Model_Observer
{
    /**
     * Add Customer Segment condition to the salesrule management
     *
     * @param Varien_Event_Observer $observer
     */
    public function addSegmentsToSalesRuleCombine(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('Enterprise_CustomerSegment_Helper_Data')->isEnabled()) {
            return;
        }
        $additional = $observer->getEvent()->getAdditional();
        $additional->setConditions(array(array(
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Customer Segment'),
            'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Segment'
        )));
    }

    /**
     * Process customer related data changing. Method can process just events with customer object
     *
     * @param   Varien_Event_Observer $observer
     */
    public function processCustomerEvent(Varien_Event_Observer $observer)
    {
        $eventName = $observer->getEvent()->getName();
        $customer  = $observer->getEvent()->getCustomer();
        $dataObject= $observer->getEvent()->getDataObject();
        $customerId= false;

        if ($customer) {
            $customerId = $customer->getId();
        }
        if (!$customerId && $dataObject) {
            $customerId = $dataObject->getCustomerId();
        }

        if ($customerId) {
            Mage::getSingleton('Enterprise_CustomerSegment_Model_Customer')->processCustomerEvent(
                $eventName,
                $customerId
            );
        }
    }

    /**
     * Match customer segments on supplied event for currently logged in customer and ran website.
     * Can be used for processing just frontend events
     *
     * @param Varien_Event_Observer $observer
     */
    public function processEvent(Varien_Event_Observer $observer)
    {
        $eventName = $observer->getEvent()->getName();
        $customer = Mage::registry('segment_customer');

        $customerSession = Mage::getSingleton('Mage_Customer_Model_Session');
        if (!$customerSession->isLoggedIn() && !$customer) {
            return $this;
        }
        if (!$customer) {
            $customer = $customerSession->getCustomer();
        }
        $website = Mage::app()->getStore()->getWebsite();
        Mage::getSingleton('Enterprise_CustomerSegment_Model_Customer')->processEvent($eventName, $customer, $website);
    }

    /**
     * Match quote customer to all customer segments.
     * Used before quote recollect in admin
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function processQuote(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $customer = $quote->getCustomer();
        if ($customer && $customer->getId()) {
            $website = $quote->getStore()->getWebsite();
            Mage::getSingleton('Enterprise_CustomerSegment_Model_Customer')->processCustomer($customer, $website);
        }
    }

    /**
     * Add field "Use in Customer Segment" for Customer and Customer Address attribute edit form
     *
     * @param Varien_Event_Observer $observer
     */
    public function enterpiseCustomerAttributeEditPrepareForm(Varien_Event_Observer $observer)
    {
        $form       = $observer->getEvent()->getForm();
        $fieldset   = $form->getElement('base_fieldset');
        $fieldset->addField('is_used_for_customer_segment', 'select', array(
            'name'      => 'is_used_for_customer_segment',
            'label'     => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Use in Customer Segment'),
            'title'     => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Use in Customer Segment'),
            'values'    => Mage::getModel('Mage_Adminhtml_Model_System_Config_Source_Yesno')->toOptionArray(),
        ));
    }
}
