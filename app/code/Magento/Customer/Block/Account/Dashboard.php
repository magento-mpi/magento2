<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account;

use Magento\Customer\Model\Customer;
use Magento\Newsletter\Model\Subscriber;

/**
 * Customer dashboard block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Dashboard extends \Magento\View\Element\Template
{
    /**
     * @var Subscriber
     */
    protected $_subscription;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_subscriberFactory = $subscriberFactory;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    /**
     * @return string
     */
    public function getAccountUrl()
    {
        return $this->_urlBuilder->getUrl('customer/account/edit', array('_secure'=>true));
    }

    /**
     * @return string
     */
    public function getAddressesUrl()
    {
        return $this->_urlBuilder->getUrl('customer/address/index', array('_secure'=>true));
    }

    /**
     * @param \Magento\Object $address
     * @return string
     */
    public function getAddressEditUrl($address)
    {
        return $this->_urlBuilder->getUrl('customer/address/edit', array('_secure'=>true, 'id'=>$address->getId()));
    }

    /**
     * @return string
     */
    public function getOrdersUrl()
    {
        return $this->_urlBuilder->getUrl('customer/order/index', array('_secure'=>true));
    }

    /**
     * @return string
     */
    public function getReviewsUrl()
    {
        return $this->_urlBuilder->getUrl('review/customer/index', array('_secure'=>true));
    }

    /**
     * @return string
     */
    public function getWishlistUrl()
    {
        return $this->_urlBuilder->getUrl('customer/wishlist/index', array('_secure'=>true));
    }

    /**
     * @return Subscriber
     */
    public function getSubscriptionObject()
    {
        if (is_null($this->_subscription)) {
            $this->_subscription = $this->_createSubscriber()->loadByCustomer($this->getCustomer());
        }

        return $this->_subscription;
    }

    /**
     * @return string
     */
    public function getManageNewsletterUrl()
    {
        return $this->getUrl('newsletter/manage');
    }

    /**
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
     * @return array|false
     */
    public function getPrimaryAddresses()
    {
        $addresses = $this->getCustomer()->getPrimaryAddresses();
        if (empty($addresses)) {
            return false;
        }
        return $addresses;
    }

    /**
     * Get back url in account dashboard
     *
     * This method is copy/pasted in:
     * \Magento\Wishlist\Block\Customer\Wishlist  - because of strange inheritance
     * \Magento\Customer\Block\Address\Book - because of secure url
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
     * @return Subscriber
     */
    protected function _createSubscriber()
    {
        return $this->_subscriberFactory->create();
    }
}
