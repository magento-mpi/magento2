<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AccountManagementInterface;

/**
 * Customer dashboard block
 */
class Dashboard extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $subscription;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        array $data = array()
    ) {
        $this->customerSession = $customerSession;
        $this->subscriberFactory = $subscriberFactory;
        $this->customerRepository = $customerRepository;
        $this->customerAccountManagement = $customerAccountManagement;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Return the Customer given the customer Id stored in the session.
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer()
    {
        return $this->customerRepository->getById($this->customerSession->getCustomerId());
    }

    /**
     * Retrieve the Url for editing the customer's account.
     *
     * @return string
     */
    public function getAccountUrl()
    {
        return $this->_urlBuilder->getUrl('customer/account/edit', array('_secure' => true));
    }

    /**
     * Retrieve the Url for customer addresses.
     *
     * @return string
     */
    public function getAddressesUrl()
    {
        return $this->_urlBuilder->getUrl('customer/address/index', array('_secure' => true));
    }

    /**
     * Retrieve the Url for editing the specified address.
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return string
     */
    public function getAddressEditUrl($address)
    {
        return $this->_urlBuilder->getUrl(
            'customer/address/edit',
            array('_secure' => true, 'id' => $address->getId())
        );
    }

    /**
     * Retrieve the Url for customer orders.
     *
     * @return string
     */
    public function getOrdersUrl()
    {
        return $this->_urlBuilder->getUrl('customer/order/index', array('_secure' => true));
    }

    /**
     * Retrieve the Url for customer reviews.
     *
     * @return string
     */
    public function getReviewsUrl()
    {
        return $this->_urlBuilder->getUrl('review/customer/index', array('_secure' => true));
    }

    /**
     * Retrieve the Url for managing customer wishlist.
     *
     * @return string
     */
    public function getWishlistUrl()
    {
        return $this->_urlBuilder->getUrl('customer/wishlist/index', array('_secure' => true));
    }

    /**
     * Retrieve the subscription object (i.e. the subscriber).
     *
     * @return \Magento\Newsletter\Model\Subscriber
     */
    public function getSubscriptionObject()
    {
        if (is_null($this->subscription)) {
            $this->subscription =
                $this->_createSubscriber()->loadByCustomerId($this->customerSession->getCustomerId());
        }

        return $this->subscription;
    }

    /**
     * Retrieve the Url for managing newsletter subscriptions.
     *
     * @return string
     */
    public function getManageNewsletterUrl()
    {
        return $this->getUrl('newsletter/manage');
    }

    /**
     * Retrieve subscription text, either subscribed or not.
     *
     * @return string
     */
    public function getSubscriptionText()
    {
        if ($this->getSubscriptionObject()->isSubscribed()) {
            return __('You subscribe to our newsletter.');
        }

        return __('You are currently not subscribed to our newsletter.');
    }

    /**
     * Retrieve the customer's primary addresses (i.e. default billing and shipping).
     *
     * @return \Magento\Customer\Api\Data\AddressInterface[]|bool
     */
    public function getPrimaryAddresses()
    {
        $addresses = array();
        $customerId = $this->getCustomer()->getId();

        if ($defaultBilling = $this->customerAccountManagement->getDefaultBillingAddress($customerId)) {
            $addresses[] = $defaultBilling;
        }

        if ($defaultShipping = $this->customerAccountManagement->getDefaultShippingAddress($customerId)) {
            if ($defaultBilling) {
                if ($defaultBilling->getId() != $defaultShipping->getId()) {
                    $addresses[] = $defaultShipping;
                }
            } else {
                $addresses[] = $defaultShipping;
            }
        }

        return empty($addresses) ? false : $addresses;
    }

    /**
     * Get back Url in account dashboard.
     *
     * This method is copy/pasted in:
     * \Magento\Wishlist\Block\Customer\Wishlist  - Because of strange inheritance
     * \Magento\Customer\Block\Address\Book - Because of secure Url
     *
     * @return string
     */
    public function getBackUrl()
    {
        // the RefererUrl must be set in appropriate controller
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }

    /**
     * Create an instance of a subscriber.
     *
     * @return \Magento\Newsletter\Model\Subscriber
     */
    protected function _createSubscriber()
    {
        return $this->subscriberFactory->create();
    }
}
