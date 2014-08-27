<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;

class SkipWebsiteRestriction
{
    /**
     * Persistent session
     *
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSession = null;

    /**
     * @param \Magento\Persistent\Helper\Session $persistentSession
     */
    public function __construct(\Magento\Persistent\Helper\Session $persistentSession)
    {
        $this->_persistentSession = $persistentSession;
    }

    /**
     * Skip website restriction and allow access for persistent customers
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $result = $observer->getEvent()->getResult();
        if ($result->getShouldProceed() && $this->_persistentSession->isPersistent()) {
            $result->setCustomerLoggedIn(true);
        }
    }
}
