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
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    public function getPrimaryShippingAddressHtml()
    {
        $address = $this->getCustomer()->getPrimaryShippingAddress();

        if( $address instanceof \Magento\Object ) {
            return $address->format('html');
        } else {
            return __('You have not set a default shipping address.');
        }
    }

    public function getPrimaryBillingAddressHtml()
    {
        $address = $this->getCustomer()->getPrimaryBillingAddress();

        if( $address instanceof \Magento\Object ) {
            return $address->format('html');
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
}
