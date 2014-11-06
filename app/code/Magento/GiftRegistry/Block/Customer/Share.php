<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;

/**
 * Customer gift registry share block
 */
class Share extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * @var mixed
     */
    protected $_formData = null;

    /**
     * Gift registry data
     *
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftRegistryData = null;

    /**
     * Customer view helper
     *
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerView;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerAddressServiceInterface $addressService
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param \Magento\Customer\Helper\View $customerView
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        CustomerAddressServiceInterface $addressService,
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        \Magento\Customer\Helper\View $customerView,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        $this->_customerView = $customerView;
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $addressService,
            $data
        );
    }

    /**
     * Retrieve form header
     *
     * @return string
     */
    public function getFormHeader()
    {
        $formHeader = $this->escapeHtml($this->getEntity()->getTitle());
        return __("Share '%1' Gift Registry", $formHeader);
    }

    /**
     * Retrieve escaped customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->escapeHtml($this->_customerView->getCustomerName($this->getCustomer()));
    }

    /**
     * Retrieve escaped customer email
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->escapeHtml($this->getCustomer()->getEmail());
    }

    /**
     * Retrieve recipients config limit
     *
     * @return int
     */
    public function getRecipientsLimit()
    {
        return (int)$this->_giftRegistryData->getRecipientsLimit();
    }

    /**
     * Retrieve entered data by key
     *
     * @param string $key
     * @return string|null
     */
    public function getFormData($key)
    {
        if (is_null($this->_formData)) {
            $this->_formData = $this->_customerSession->getData('sharing_form', true);
        }
        if (!$this->_formData || !isset($this->_formData[$key])) {
            return null;
        } else {
            return $this->escapeHtml($this->_formData[$key]);
        }
    }

    /**
     * Return back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('giftregistry');
    }

    /**
     * Return form send url
     *
     * @return string
     */
    public function getSendUrl()
    {
        return $this->getUrl('giftregistry/index/send', array('id' => $this->getEntity()->getId()));
    }
}
