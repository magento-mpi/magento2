<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer balance as an additional payment option during checkout
 *
 * @category   Magento
 * @package    Magento_CustomerBalance
 */
class Magento_CustomerBalance_Block_Checkout_Onepage_Payment_Additional extends Magento_Core_Block_Template
{
    /**
     * Customer balance instance
     *
     * @var Magento_CustomerBalance_Model_Balance
     */
    protected $_balanceModel = null;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_CustomerBalance_Model_BalanceFactory
     */
    protected $_balanceFactory;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_CustomerBalance_Model_BalanceFactory $balanceFactory
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_CustomerBalance_Model_BalanceFactory $balanceFactory,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_balanceFactory = $balanceFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get quote instance
     *
     * @return Magento_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Getter
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_getQuote();
    }

    /**
     * Get balance instance
     *
     * @return Magento_CustomerBalance_Model_Balance
     */
    protected function _getBalanceModel()
    {
        if (is_null($this->_balanceModel)) {
            $this->_balanceModel = $this->_balanceFactory->create()
                ->setCustomer($this->_getCustomer())
                ->setWebsiteId($this->_storeManager->getStore()->getWebsiteId());

            //load customer balance for customer in case we have
            //registered customer and this is not guest checkout
            if ($this->_getCustomer()->getId()) {
                $this->_balanceModel->loadByCustomer();
            }
        }
        return $this->_balanceModel;
    }

    /**
     * Get customer instance
     *
     * @return Magento_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    /**
     * Can display customer balance container
     *
     * @return bool
     */
    public function isDisplayContainer()
    {
        if (!$this->_getCustomer()->getId()) {
            return false;
        }

        if (!$this->getBalance()) {
            return false;
        }

        return true;
    }

    /**
     * Check whether customer balance is allowed as additional payment option
     *
     * @return bool
     */
    public function isAllowed()
    {
        if (!$this->isDisplayContainer()) {
            return false;
        }

        if (!$this->getAmountToCharge()) {
            return false;
        }

        return true;
    }

    /**
     * Get balance amount
     *
     * @return float
     */
    public function getBalance()
    {
        if (!$this->_getCustomer()->getId()) {
            return 0;
        }
        return $this->_getBalanceModel()->getAmount();
    }

    /**
     * Get balance amount to be charged
     *
     * @return float
     */
    public function getAmountToCharge()
    {
        if ($this->isCustomerBalanceUsed()) {
            return $this->_getQuote()->getCustomerBalanceAmountUsed();
        }

        return min($this->getBalance(), $this->_getQuote()->getBaseGrandTotal());
    }

    /**
     * Check whether customer balance is used in current quote
     *
     * @return bool
     */
    public function isCustomerBalanceUsed() {
        return $this->_getQuote()->getUseCustomerBalance();
    }

    /**
     * Check whether customer balance fully covers quote
     *
     * @return bool
     */
    public function isFullyPaidAfterApplication()
    {
        return $this->_getBalanceModel()->isFullAmountCovered($this->_getQuote(), true);
    }
}
