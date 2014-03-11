<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Observer\Backend;

use Magento\Customer\Service\V1\Data\Customer as CustomerData;

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
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Config\Share $config
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Config\Share $config,
        \Magento\Sales\Model\QuoteFactory $quoteFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        $this->_quoteFactory = $quoteFactory;
    }

    /**
     * Set new customer group to all his quotes
     *
     * @param \Magento\Event\Observer $observer
     */
    public function dispatch(\Magento\Event\Observer $observer)
    {
        /** @var CustomerData $customerDataObject */
        $customerDataObject = $observer->getEvent()->getCustomerDataObject();
        /** @var CustomerData $origCustomerDataObject */
        $origCustomerDataObject = $observer->getEvent()->getOrigCustomerDataObject();
        if ($customerDataObject->getGroupId() !== $origCustomerDataObject->getGroupId()) {
            /**
             * It is needed to process customer's quotes for all websites
             * if customer accounts are shared between all of them
             */
            /** @var $websites \Magento\Core\Model\Website[] */
            $websites = $this->_config->isWebsiteScope()
                ? array($this->_storeManager->getWebsite($customerDataObject->getWebsiteId()))
                : $this->_storeManager->getWebsites();

            foreach ($websites as $website) {
                $quote = $this->_quoteFactory->create();
                $quote->setWebsite($website);
                $quote->loadByCustomer($customerDataObject->getId());
                if ($quote->getId()) {
                    $quote->setCustomerGroupId($customerDataObject->getGroupId());
                    $quote->collectTotals();
                    $quote->save();
                }
            }
        }
    }
}
