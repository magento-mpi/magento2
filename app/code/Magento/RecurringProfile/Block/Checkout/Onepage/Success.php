<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Block\Checkout\Onepage;

/**
 * Recurring Profile information on Order success page
 */
class Success extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\RecurringProfile\Model\Resource\Profile\CollectionFactory
     */
    protected $_recurringProfileCollectionFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\RecurringProfile\Model\Resource\Profile\CollectionFactory $recurringProfileCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\RecurringProfile\Model\Resource\Profile\CollectionFactory $recurringProfileCollectionFactory,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_recurringProfileCollectionFactory = $recurringProfileCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Getter for recurring profile view page
     *
     * @param $profile
     * @return string
     */
    public function getProfileUrl(\Magento\Object $profile)
    {
        return $this->getUrl('sales/recurringProfile/view', array('profile' => $profile->getId()));
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_prepareLastRecurringProfiles();
        return parent::_beforeToHtml();
    }

    /**
     * Prepare recurring payment profiles from the session
     */
    protected function _prepareLastRecurringProfiles()
    {
        $profileIds = $this->_checkoutSession->getLastRecurringProfileIds();
        if ($profileIds && is_array($profileIds)) {
            $collection = $this->_recurringProfileCollectionFactory->create()
                ->addFieldToFilter('profile_id', array('in' => $profileIds));
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
