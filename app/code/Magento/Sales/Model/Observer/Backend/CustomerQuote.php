<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Observer\Backend;

class CustomerQuote
{
    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    protected $_config;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Sales\Model\Quote
     */
    protected $_quote;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Config\Share $config
     * @param \Magento\Sales\Model\Quote $quote
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Config\Share $config,
        \Magento\Sales\Model\Quote $quote
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
        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = $observer->getEvent()->getCustomer();

        if ($customer->getGroupId() !== $customer->getOrigData('group_id')) {
            /**
             * It is needed to process customer's quotes for all websites
             * if customer accounts are shared between all of them
             */
            /** @var $websites \Magento\Core\Model\Website[] */
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
