<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout success page
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Onepage_Success extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var Magento_Sales_Model_Billing_AgreementFactory
     */
    protected $_agreementFactory;

    /**
     * @var Magento_Sales_Model_Resource_Recurring_Profile_Collection
     */
    protected $_profileCollFactory;

    /**
     * @var Magento_Sales_Model_Order_Config
     */
    protected $_orderConfig;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Sales_Model_Billing_AgreementFactory $agreementFactory
     * @param Magento_Sales_Model_Resource_Recurring_Profile_Collection $profileCollFactory
     * @param Magento_Sales_Model_Order_Config $orderConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Customer_Model_Session $customerSession,
        Magento_Sales_Model_OrderFactory $orderFactory,
        Magento_Sales_Model_Billing_AgreementFactory $agreementFactory,
        Magento_Sales_Model_Resource_Recurring_Profile_Collection $profileCollFactory,
        Magento_Sales_Model_Order_Config $orderConfig,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_orderFactory = $orderFactory;
        $this->_agreementFactory = $agreementFactory;
        $this->_profileCollFactory = $profileCollFactory;
        $this->_orderConfig = $orderConfig;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * See if the order has state, visible on frontend
     *
     * @return bool
     */
    public function isOrderVisible()
    {
        return (bool)$this->_getData('is_order_visible');
    }

    /**
     * Getter for recurring profile view page
     *
     * @param $profile
     * @return string
     */
    public function getProfileUrl(Magento_Object $profile)
    {
        return $this->getUrl('sales/recurring_profile/view', array('profile' => $profile->getId()));
    }

    /**
     * Initialize data and prepare it for output
     */
    protected function _beforeToHtml()
    {
        $this->_prepareLastOrder();
        $this->_prepareLastBillingAgreement();
        $this->_prepareLastRecurringProfiles();
        return parent::_beforeToHtml();
    }

    /**
     * Get last order ID from session, fetch it and check whether it can be viewed, printed etc
     */
    protected function _prepareLastOrder()
    {
        $orderId = $this->_checkoutSession->getLastOrderId();
        if ($orderId) {
            $order = $this->_orderFactory->create()->load($orderId);
            if ($order->getId()) {
                $isVisible = !in_array($order->getState(), $this->_orderConfig->getInvisibleOnFrontStates());
                $this->addData(array(
                    'is_order_visible' => $isVisible,
                    'view_order_url' => $this->getUrl('sales/order/view/', array('order_id' => $orderId)),
                    'print_url' => $this->getUrl('sales/order/print', array('order_id'=> $orderId)),
                    'can_print_order' => $isVisible,
                    'can_view_order'  => $this->_customerSession->isLoggedIn() && $isVisible,
                    'order_id'  => $order->getIncrementId(),
                ));
            }
        }
    }

    /**
     * Prepare billing agreement data from an identifier in the session
     */
    protected function _prepareLastBillingAgreement()
    {
        $agreementId = $this->_checkoutSession->getLastBillingAgreementId();
        $customerId = $this->_customerSession->getCustomerId();
        if ($agreementId && $customerId) {
            $agreement = $this->_agreementFactory->create()->load($agreementId);
            if ($agreement->getId() && $customerId == $agreement->getCustomerId()) {
                $this->addData(array(
                    'agreement_ref_id' => $agreement->getReferenceId(),
                    'agreement_url' => $this->getUrl('sales/billing_agreement/view',
                        array('agreement' => $agreementId)
                    ),
                ));
            }
        }
    }

    /**
     * Prepare recurring payment profiles from the session
     */
    protected function _prepareLastRecurringProfiles()
    {
        $profileIds = $this->_checkoutSession->getLastRecurringProfileIds();
        if ($profileIds && is_array($profileIds)) {
            $collection = $this->_profileCollFactory->create()->getCollection()
                ->addFieldToFilter('profile_id', array('in' => $profileIds))
            ;
            $profiles = array();
            foreach ($collection as $profile) {
                $profiles[] = $profile;
            }
            if ($profiles) {
                $this->setRecurringProfiles($profiles);
                if ($this->_customerSession->isLoggedIn()) {
                    $this->setCanViewProfiles(true);
                }
            }
        }
    }
}
