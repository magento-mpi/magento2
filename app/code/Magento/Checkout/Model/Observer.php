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
 * Checkout observer model
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Model;

class Observer
{
    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param Session $checkoutSession
     * @param \Magento\Message\ManagerInterface $messageManager
     */
    public function __construct(
        Session $checkoutSession,
        \Magento\Message\ManagerInterface $messageManager
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->messageManager = $messageManager;
    }

    /**
     * @return void
     */
    public function unsetAll()
    {
        $this->_checkoutSession->clearQuote()->clearStorage();
    }

    /**
     * @return void
     */
    public function loadCustomerQuote()
    {
        try {
            $this->_checkoutSession->loadCustomerQuote();
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Load customer quote error'));
        }
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function salesQuoteSaveAfter($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        /* @var $quote \Magento\Sales\Model\Quote */
        if ($quote->getIsCheckoutCart()) {
            $this->_checkoutSession->getQuoteId($quote->getId());
        }
    }
}
