<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Sales_Model_Observer_Backend_CustomerQuote
{
    /**
     * @var Magento_Customer_Model_Config_Share
     */
    protected $_config;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Customer_Model_Config_Share $config
     * @param Magento_Sales_Model_Quote $quote
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Customer_Model_Config_Share $config,
        Magento_Sales_Model_Quote $quote
    ) {
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        $this->_quote = $quote;
    }

    /**
     * Set new customer group to all his quotes
     *
     * @param \Magento\Event\Observer $observer
     */
    public function dispatch(\Magento\Event\Observer $observer)
    {
        /** @var $customer Magento_Customer_Model_Customer */
        $customer = $observer->getEvent()->getCustomer();

        if ($customer->getGroupId() !== $customer->getOrigData('group_id')) {
            /**
             * It is needed to process customer's quotes for all websites
             * if customer accounts are shared between all of them
             */
            /** @var $websites Magento_Core_Model_Website[] */
            $websites = $this->_config->isWebsiteScope() ?
                array($this->_storeManager->getWebsite($customer->getWebsiteId())) :
                $this->_storeManager->getWebsites();

            foreach ($websites as $website) {
                $this->_quote->setWebsite($website);
                $this->_quote->loadByCustomer($customer);

                if ($this->_quote->getId()) {
                    $this->_quote->setCustomerGroupId($customer->getGroupId());
                    $this->_quote->collectTotals();
                    $this->_quote->save();
                }
            }
        }
    }
}
