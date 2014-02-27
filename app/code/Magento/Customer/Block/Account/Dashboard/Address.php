<?php
/**
 * Customer dashboard addresses section
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account\Dashboard;

use Magento\Exception\NoSuchEntityException;

class Address extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerServiceInterface
     */
    protected $_customerService;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $_addressConfig;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface
     */
    protected $_addressService;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Service\V1\CustomerServiceInterface $customerService
     * @param \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Service\V1\CustomerServiceInterface $customerService,
        \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService,
        \Magento\Customer\Model\Address\Config $addressConfig,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerService = $customerService;
        $this->_addressService = $addressService;
        $this->_addressConfig = $addressConfig;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Get the logged in customer
     *
     * @return \Magento\Customer\Service\V1\Dto\Customer
     */
    public function getCustomer()
    {
        try {
            return $this->_customerService->getCustomer($this->_customerSession->getId());
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * HTML for Shipping Address
     *
     * @return string
     */
    public function getPrimaryShippingAddressHtml()
    {
        try {
            $address = $this->_addressService->getDefaultShippingAddress($this->_customerSession->getCustomerId());
        } catch (NoSuchEntityException $e) {
            return __('You have not set a default shipping address.');
        }

        if ($address) {
            return $this->_getAddressHtml($address);
        } else {
            return __('You have not set a default shipping address.');
        }
    }

    /**
     * HTML for Billing Address
     *
     * @return string
     */
    public function getPrimaryBillingAddressHtml()
    {
        try {
            $address = $this->_addressService->getDefaultBillingAddress($this->_customerSession->getCustomerId());
        } catch (NoSuchEntityException $e) {
            return __('You have not set a default billing address.');
        }

        if ($address) {
            return $this->_getAddressHtml($address);
        } else {
            return __('You have not set a default billing address.');
        }
    }

    public function getPrimaryShippingAddressEditUrl()
    {
        if (is_null($this->getCustomer())) {
            return '';
        } else {
            return $this->_urlBuilder
                ->getUrl('customer/address/edit', ['id'=>$this->getCustomer()->getDefaultShipping()]);
        }
    }

    public function getPrimaryBillingAddressEditUrl()
    {
        if (is_null($this->getCustomer())) {
            return '';
        } else {
            return $this->_urlBuilder
                ->getUrl('customer/address/edit', ['id'=>$this->getCustomer()->getDefaultBilling()]);
        }
    }

    public function getAddressBookUrl()
    {
        return $this->getUrl('customer/address/');
    }

    /**
     * Render an address as HTML and return the result
     *
     * @param \Magento\Customer\Service\V1\Dto\Address $address
     * @return string
     */
    protected function _getAddressHtml($address)
    {
        /** @var \Magento\Customer\Block\Address\Renderer\RendererInterface $renderer */
        $renderer = $this->_addressConfig->getFormatByCode('html')->getRenderer();
        return $renderer->renderArray($address->getAttributes());
    }
}

