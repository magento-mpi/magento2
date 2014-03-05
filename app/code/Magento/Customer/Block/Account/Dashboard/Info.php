<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Account\Dashboard;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;
use Magento\Exception\NoSuchEntityException;

/**
 * Dashboard Customer Info
 */
class Info extends \Magento\View\Element\Template
{
    /**
     * Cached subscription object
     *
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $_subscription;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @var CustomerMetadataServiceInterface
     */
    protected $_metadataService;

    /** @var  CustomerAccountServiceInterface */
    protected $_customerAccountService;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param CustomerMetadataServiceInterface $metadataService
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerAccountServiceInterface $customerAccountService,
        CustomerMetadataServiceInterface $metadataService,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerAccountService = $customerAccountService;
        $this->_metadataService = $metadataService;
        $this->_subscriberFactory = $subscriberFactory;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Returns the Magento Customer Model for this block
     *
     * @return \Magento\Customer\Service\V1\Dto\Customer
     */
    public function getCustomer()
    {
        try {
            return $this->_customerAccountService->getCustomer($this->_customerSession->getId());
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Get the full name of a customer
     *
     * @return string full name
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getName()
    {
        $name = '';

        $customer = $this->getCustomer();

        $prefixMetadata = $this->_getAttributeMetadata('prefix');
        if (!is_null($prefixMetadata) && $prefixMetadata->isVisible() && $customer->getPrefix()) {
            $name .= $customer->getPrefix() . ' ';
        }
        $name .= $customer->getFirstname();
        $midNameMetadata = $this->_getAttributeMetadata('middlename');
        if (!is_null($midNameMetadata) && $midNameMetadata->isVisible() && $customer->getMiddlename()) {
            $name .= ' ' . $customer->getMiddlename();
        }
        $name .=  ' ' . $customer->getLastname();
        $suffixMetadata = $this->_getAttributeMetadata('suffix');
        if (!is_null($suffixMetadata) && $suffixMetadata->isVisible() && $customer->getSuffix()) {
            $name .= ' ' . $customer->getSuffix();
        }
        return $name;
    }

    public function getChangePasswordUrl()
    {
        return $this->_urlBuilder->getUrl('*/account/edit/changepass/1');
    }

    /**
     * Get Customer Subscription Object Information
     *
     * @return \Magento\Newsletter\Model\Subscriber
     */
    public function getSubscriptionObject()
    {
        if (!$this->_subscription) {
            $this->_subscription = $this->_createSubscriber();
            $customer = $this->getCustomer();
            if ($customer) {
                $this->_subscription->loadByEmail($customer->getEmail());
            }
        }
        return $this->_subscription;
    }

    /**
     * Gets Customer subscription status
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsSubscribed()
    {
        return $this->getSubscriptionObject()->isSubscribed();
    }

    /**
     * Newsletter module availability
     *
     * @return bool
     */
    public function isNewsletterEnabled()
    {
        return $this->getLayout()->getBlockSingleton('Magento\Customer\Block\Form\Register')->isNewsletterEnabled();
    }

    /**
     * @return \Magento\Newsletter\Model\Subscriber
     */
    protected function _createSubscriber()
    {
        return $this->_subscriberFactory->create();
    }

    /**
     * @param string $attributeCode
     * @return \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata|null
     */
    protected function _getAttributeMetadata($attributeCode)
    {
        try {
            return $this->_metadataService->getCustomerAttributeMetadata($attributeCode);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}
