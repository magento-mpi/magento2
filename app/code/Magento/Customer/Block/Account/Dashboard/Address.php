<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account\Dashboard;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Address\Mapper;

/**
 * Class to manage customer dashboard addresses section
 */
class Address extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $_addressConfig;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomerAddress
     */
    protected $currentCustomerAddress;

    /**
     * @var Mapper
     */
    protected $addressMapper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Customer\Helper\Session\CurrentCustomerAddress $currentCustomerAddress
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param Mapper $addressMapper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Customer\Helper\Session\CurrentCustomerAddress $currentCustomerAddress,
        \Magento\Customer\Model\Address\Config $addressConfig,
        Mapper $addressMapper,
        array $data = array()
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->currentCustomerAddress = $currentCustomerAddress;
        $this->_addressConfig = $addressConfig;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->addressMapper = $addressMapper;
    }

    /**
     * Get the logged in customer
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        try {
            return $this->currentCustomer->getCustomer();
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
            $address = $this->currentCustomerAddress->getDefaultShippingAddress();
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
            $address = $this->currentCustomerAddress->getDefaultBillingAddress();
        } catch (NoSuchEntityException $e) {
            return __('You have not set a default billing address.');
        }

        if ($address) {
            return $this->_getAddressHtml($address);
        } else {
            return __('You have not set a default billing address.');
        }
    }

    /**
     * @return string
     */
    public function getPrimaryShippingAddressEditUrl()
    {
        if (is_null($this->getCustomer()) || is_null($this->currentCustomerAddress->getDefaultShippingAddress())) {
            return '';
        } else {
            $address = $this->currentCustomerAddress->getDefaultShippingAddress();
            if ($address) {
                $addressId = $address->getId();
            } else {
                $addressId = null;
            }
            return $this->_urlBuilder->getUrl(
                'customer/address/edit',
                array('id' => $addressId)
            );
        }
    }

    /**
     * @return string
     */
    public function getPrimaryBillingAddressEditUrl()
    {
        if (is_null($this->getCustomer()) || is_null($this->currentCustomerAddress->getDefaultBillingAddress())) {
            return '';
        } else {
            $address = $this->currentCustomerAddress->getDefaultBillingAddress();
            if ($address) {
                $addressId = $address->getId();
            } else {
                $addressId = null;
            }
            return $this->_urlBuilder->getUrl(
                'customer/address/edit',
                array('id' => $addressId)
            );
        }
    }

    /**
     * @return string
     */
    public function getAddressBookUrl()
    {
        return $this->getUrl('customer/address/');
    }

    /**
     * Render an address as HTML and return the result
     *
     * @param AddressInterface $address
     * @return string
     */
    protected function _getAddressHtml($address)
    {
        /** @var \Magento\Customer\Block\Address\Renderer\RendererInterface $renderer */
        $renderer = $this->_addressConfig->getFormatByCode('html')->getRenderer();
        return $renderer->renderArray($this->addressMapper->toFlatArray($address));
    }
}
