<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * CustomerSegment observer
 *
 */
class Enterprise_CustomerSegment_Model_Observer
{
    /**
     * Add select element is_used_for_customer_segment into form edit attribute
     *
     * @param Varien_Event_Observer $observer
     */
    public function addProductAttributeField(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_customersegment')->isEnabled()) {
            return;
        }
        /* @var $form Varien_Data_Form */
        $form = $observer->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('front_fieldset');
        $fieldset->addField('is_used_for_customer_segment', 'select', array(
            'name' => 'is_used_for_customer_segment',
            'label' => Mage::helper('enterprise_customersegment')->__('Use for Customer Segment Conditions'),
            'title' => Mage::helper('enterprise_customersegment')->__('Use for Customer Segment Conditions'),
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ), 'is_used_for_customer_segment');
    }

    /**
     * Add Customer Segment condition to the salesrule management
     *
     * @param Varien_Event_Observer $observer
     */
    public function addSegmentsToSalesRuleCombine(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_customersegment')->isEnabled()) {
            return;
        }
        $additional = $observer->getEvent()->getAdditional();
        $additional->setConditions(array(array(
            'label' => Mage::helper('enterprise_customersegment')->__('Customer Segment'),
            'value' => 'enterprise_customersegment/segment_condition_segment'
        )));
    }

    /**
     * Match customer segments on supplied event for currently logged in customer and ran website.
     * Can be used for processing frontend events
     *
     * @param Varien_Event_Observer $observer
     */
    public function processEvent(Varien_Event_Observer $observer)
    {
        $eventName = $observer->getEvent()->getName();
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn()) {
            return $this;
        }
        $customer = $customerSession->getCustomer();
        $website = Mage::app()->getStore()->getWebsite();
        Mage::getSingleton('enterprise_customersegment/customer')->processEvent($eventName, $customer, $website);
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
            Mage::getSingleton('enterprise_customersegment/customer')->processCustomer($customer, $website);
        }
    }
}
