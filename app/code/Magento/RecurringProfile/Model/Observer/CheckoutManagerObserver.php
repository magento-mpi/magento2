<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Model\Observer;

class CheckoutManagerObserver
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\RecurringProfile\Model\QuoteImporter
     */
    protected $_quoteImporter;

    /**
     * @var array
     */
    protected $_recurringProfiles = null;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\RecurringProfile\Model\QuoteImporter $quoteImporter
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\RecurringProfile\Model\QuoteImporter $quoteImporter
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_quoteImporter = $quoteImporter;
    }

    /**
     * Submit recurring profiles
     *
     * @param \Magento\Event\Observer $observer
     * @throws \Magento\Core\Exception
     */
    public function submitRecurringPaymentProfiles($observer)
    {
        $this->_recurringProfiles = $this->_quoteImporter->import($observer->getEvent()->getQuote());
        foreach ($this->_recurringProfiles as $profile) {
            if (!$profile->isValid()) {
                throw new \Magento\Core\Exception($profile->getValidationErrors());
            }
            $profile->submit();
        }
    }

    /**
     * Add recurring profile ids to session
     */
    public function addRecurringProfileIdsToSession()
    {
        if ($this->_recurringProfiles) {
            $ids = array();
            foreach ($this->_recurringProfiles as $profile) {
                $ids[] = $profile->getId();
            }
            $this->_checkoutSession->setLastRecurringProfileIds($ids);
        }
    }
}
 