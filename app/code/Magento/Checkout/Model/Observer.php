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
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Message\ManagerInterface $messageManager
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->messageManager = $messageManager;
    }

    public function unsetAll()
    {
        $this->_checkoutSession->clearQuote()->clearStorage();
    }

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

    public function salesQuoteSaveAfter($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        /* @var $quote \Magento\Sales\Model\Quote */
        if ($quote->getIsCheckoutCart()) {
            $this->_checkoutSession->getQuoteId($quote->getId());
        }
    }
}
