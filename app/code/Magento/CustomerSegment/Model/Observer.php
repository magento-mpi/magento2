<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CustomerSegment observer
 */
class Magento_CustomerSegment_Model_Observer
{
    /**
     * @var Magento_CustomerSegment_Helper_Data
     */
    private $_segmentHelper;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Backend_Model_Config_Source_Yesno
     */
    protected $_configSourceYesno;

    /**
     * @var Magento_CustomerSegment_Model_Customer
     */
    protected $_customer;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Store list manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_CustomerSegment_Model_Customer $customer
     * @param Magento_Backend_Model_Config_Source_Yesno $configSourceYesno
     * @param Magento_CustomerSegment_Helper_Data $segmentHelper
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Customer_Model_Session $customerSession,
        Magento_CustomerSegment_Model_Customer $customer,
        Magento_Backend_Model_Config_Source_Yesno $configSourceYesno,
        Magento_CustomerSegment_Helper_Data $segmentHelper,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_customer = $customer;
        $this->_configSourceYesno = $configSourceYesno;
        $this->_coreRegistry = $coreRegistry;
        $this->_segmentHelper = $segmentHelper;
    }

    /**
     * Add Customer Segment condition to the salesrule management
     *
     * @param Magento_Event_Observer $observer
     */
    public function addSegmentsToSalesRuleCombine(Magento_Event_Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        $additional = $observer->getEvent()->getAdditional();
        $additional->setConditions(array(array(
            'label' => __('Customer Segment'),
            'value' => 'Magento_CustomerSegment_Model_Segment_Condition_Segment'
        )));
    }

    /**
     * Process customer related data changing. Method can process just events with customer object
     *
     * @param   Magento_Event_Observer $observer
     */
    public function processCustomerEvent(Magento_Event_Observer $observer)
    {
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
            $this->_customer->processCustomerEvent($observer->getEvent()->getName(), $customerId);
        }
    }

    /**
     * Match customer segments on supplied event for currently logged in customer or visitor and current website.
     * Can be used for processing just frontend events
     *
     * @param Magento_Event_Observer $observer
     */
    public function processEvent(Magento_Event_Observer $observer)
    {
        $customer = $this->_coreRegistry->registry('segment_customer');

        // For visitors use customer instance from customer session
        if (!$customer) {
            $customer = $this->_customerSession->getCustomer();
        }

        $this->_customer->processEvent($observer->getEvent()->getName(), $customer,
            $this->_storeManager->getStore()->getWebsite());
    }

    /**
     * Match quote customer to all customer segments.
     * Used before quote recollect in admin
     *
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function processQuote(Magento_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $customer = $quote->getCustomer();
        if ($customer && $customer->getId()) {
            $website = $quote->getStore()->getWebsite();
            $this->_customer->processCustomer($customer, $website);
        }
    }

    /**
     * Add field "Use in Customer Segment" for Customer and Customer Address attribute edit form
     *
     * @param Magento_Event_Observer $observer
     */
    public function enterpiseCustomerAttributeEditPrepareForm(Magento_Event_Observer $observer)
    {
        $form       = $observer->getEvent()->getForm();
        $fieldset   = $form->getElement('base_fieldset');
        $fieldset->addField('is_used_for_customer_segment', 'select', array(
            'name'      => 'is_used_for_customer_segment',
            'label'     => __('Use in Customer Segment'),
            'title'     => __('Use in Customer Segment'),
            'values'    => $this->_configSourceYesno->toOptionArray(),
        ));
    }

    /**
     * Add Customer Segment form fields to Target Rule form
     *
     * Observe  targetrule_edit_tab_main_after_prepare_form event
     *
     * @param Magento_Event_Observer $observer
     */
    public function addFieldsToTargetRuleForm(Magento_Event_Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /* @var $form Magento_Data_Form */
        $form = $observer->getEvent()->getForm();
        /** @var Magento_Object $model */
        $model = $observer->getEvent()->getModel();
        /** @var Magento_Core_Block_Abstract $block */
        $block = $observer->getEvent()->getBlock();

        /** @var Magento_Backend_Block_Widget_Form_Element_Dependence $fieldDependencies */
        $fieldDependencies = $block->getLayout()->createBlock('Magento_Backend_Block_Widget_Form_Element_Dependence');
        $block->setChild('form_after', $fieldDependencies);

        $this->_segmentHelper->addSegmentFieldsToForm($form, $model, $fieldDependencies);
    }
}
