<?php
/**
 * Customer dashboard addresses section
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Account\Dashboard;

class Address extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $_addressConfig;

    /**
     * @var \Magento\Customer\Service\V1\CustomerCurrentServiceInterface
     */
    protected $customerCurrentService;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAddressCurrentServiceInterface
     */
    protected $customerAddressCurrentService;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Service\V1\CustomerCurrentServiceInterface $customerCurrentService
     * @param \Magento\Customer\Service\V1\CustomerAddressCurrentServiceInterface $customerAddressCurrentService
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context                              $context,
        \Magento\Customer\Service\V1\CustomerCurrentServiceInterface        $customerCurrentService,
        \Magento\Customer\Service\V1\CustomerAddressCurrentServiceInterface $customerAddressCurrentService,
        \Magento\Customer\Model\Address\Config                              $addressConfig,
        array $data = array()
    ) {
        $this->customerCurrentService           = $customerCurrentService;
        $this->customerAddressCurrentService    = $customerAddressCurrentService;
        $this->_addressConfig                   = $addressConfig;
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
            return $this->customerCurrentService->getCustomer();
        } catch (\Magento\Exception\NoSuchEntityException $e) {
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
            $address = $this->customerAddressCurrentService->getDefaultShippingAddress();
        } catch (\Magento\Exception\NoSuchEntityException $e) {
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
            $address = $this->customerAddressCurrentService->getDefaultBillingAddress();
        } catch (\Magento\Exception\NoSuchEntityException $e) {
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
            return $this->_urlBuilder->getUrl('customer/address/edit',
                array('id'=>$this->getCustomer()->getDefaultShipping()));
        }
    }

    public function getPrimaryBillingAddressEditUrl()
    {
        if (is_null($this->getCustomer())) {
            return '';
        } else {
            return $this->_urlBuilder->getUrl('customer/address/edit',
                array('id'=>$this->getCustomer()->getDefaultBilling()));
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

