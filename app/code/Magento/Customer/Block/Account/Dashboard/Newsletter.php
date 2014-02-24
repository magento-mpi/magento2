<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account\Dashboard;

use Magento\Newsletter\Model\Subscriber;

/**
 * Dashboard neswletter info
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Newsletter extends \Magento\View\Element\Template
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
     * @return Subscriber
     */
    public function getSubscriptionObject()
    {
        if (is_null($this->_subscription)) {
            $this->_subscription = $this->_createSubscriber()->loadByCustomer($this->_customerSession->getCustomer());
        }
        return $this->_subscription;
    }

    /**
     * @return \Magento\Newsletter\Model\SubscriberFactory
     */
    protected function _createSubscriber()
    {
        return $this->_subscriberFactory->create();
    }
}
