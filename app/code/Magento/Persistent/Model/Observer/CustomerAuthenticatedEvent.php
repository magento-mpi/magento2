<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Model\Observer;

class CustomerAuthenticatedEvent
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Request http
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_requestHttp;

    /**
     * @var \Magento\Persistent\Model\QuoteManager
     */
    protected $quoteManager;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Persistent\Model\QuoteManager $quoteManager
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Persistent\Model\QuoteManager $quoteManager
    ) {
        $this->_customerSession = $customerSession;
        $this->_requestHttp = $request;
        $this->quoteManager = $quoteManager;
    }

    /**
     * Reset session data when customer re-authenticates
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute($observer)
    {
        $this->_customerSession->setCustomerId(null)->setCustomerGroupId(null);

        if ($this->_requestHttp->getParam('context') != 'checkout') {
            $this->quoteManager->expire();
            return;
        }

        $this->quoteManager->setGuest();
    }
} 
