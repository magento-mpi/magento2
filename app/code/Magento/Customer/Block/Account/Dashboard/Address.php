<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer dashboard addresses section
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Customer\Block\Account\Dashboard;

class Address extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Service\CustomerV1Interface
     */
    protected $_customerService;

    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $_addressConfig;
    
    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Service\CustomerV1Interface $customerService
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Service\CustomerV1Interface $customerService,
        \Magento\Customer\Model\Address\Config $addressConfig,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerService = $customerService;
        $this->_addressConfig = $addressConfig;
        parent::__construct($context, $data);
    }

    /**
     * Get the logged in customer
     *
     * @return \Magento\Customer\Service\Entity\V1\Customer
     */
    public function getCustomer()
    {
        return $this->_customerService->getCustomer($this->_customerSession->getId());
    }

    /**
     * HTML for Shipping Address
     *
     * @return string
     */
    public function getPrimaryShippingAddressHtml()
    {
        $address = $this->_customerService->getDefaultShippingAddress($this->_customerSession->getCustomerId());

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
        $address = $this->_customerService->getDefaultBillingAddress($this->_customerSession->getCustomerId());

        if ($address) {
            return $this->_getAddressHtml($address);
        } else {
            return __('You have not set a default billing address.');
        }
    }

    public function getPrimaryShippingAddressEditUrl()
    {
        return $this->_urlBuilder->getUrl('customer/address/edit', array('id'=>$this->getCustomer()->getDefaultShipping()));
    }

    public function getPrimaryBillingAddressEditUrl()
    {
        return $this->_urlBuilder->getUrl('customer/address/edit', array('id'=>$this->getCustomer()->getDefaultBilling()));
    }

    public function getAddressBookUrl()
    {
        return $this->getUrl('customer/address/');
    }

    /**
     * Render an address as HTML and return the result
     *
     * @param \Magento\Customer\Service\Entity\V1\Address $address
     * @return string
     */
    protected function _getAddressHtml($address)
    {
        /** @var \Magento\Customer\Block\Address\Renderer\RendererInterface $renderer */
        $renderer = $this->_addressConfig->getFormatByCode('html')->getRenderer();
        return $renderer->render($address->getAttributes());
    }
}

