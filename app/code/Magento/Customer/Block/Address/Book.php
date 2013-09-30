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
 * Customer address book block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Block_Address_Book extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($coreData, $context, $data);
    }

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')
            ->setTitle(__('Address Book'));

        return parent::_prepareLayout();
    }

    public function getAddAddressUrl()
    {
        return $this->getUrl('customer/address/new', array('_secure'=>true));
    }

    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/', array('_secure'=>true));
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('customer/address/delete');
    }

    public function getAddressEditUrl($address)
    {
        return $this->getUrl('customer/address/edit', array('_secure'=>true, 'id'=>$address->getId()));
    }

    public function getPrimaryBillingAddress()
    {
        return $this->getCustomer()->getPrimaryBillingAddress();
    }

    public function getPrimaryShippingAddress()
    {
        return $this->getCustomer()->getPrimaryShippingAddress();
    }

    public function hasPrimaryAddress()
    {
        return $this->getPrimaryBillingAddress() || $this->getPrimaryShippingAddress();
    }

    public function getAdditionalAddresses()
    {
        $addresses = $this->getCustomer()->getAdditionalAddresses();
        return empty($addresses) ? false : $addresses;
    }

    public function getAddressHtml($address)
    {
        return $address->format('html');
    }

    public function getCustomer()
    {
        $customer = $this->getData('customer');
        if (is_null($customer)) {
            $customer = $this->_customerSession->getCustomer();
            $this->setData('customer', $customer);
        }
        return $customer;
    }

    /**
     * @return int
     */
    public function getDefaultBilling()
    {
        return $this->_customerSession->getCustomer()->getDefaultBilling();
    }

    /**
     * @param int $address
     * @return Magento_Customer_Model_Address
     */
    public function getAddressById($address)
    {
        return $this->_customerSession->getCustomer()->getAddressById($address);
    }

    /**
     * @return int
     */
    public function getDefaultShipping()
    {
        return $this->_customerSession->getCustomer()->getDefaultShipping();
    }
}
