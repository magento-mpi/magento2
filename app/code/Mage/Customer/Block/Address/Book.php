<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer address book block
 */
class Mage_Customer_Block_Address_Book extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Customer_Model_Session
     */
    protected $_sessionModel;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Customer_Model_Session $sessionModel
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Customer_Model_Session $sessionModel,
        array $data = array()
    ) {
        $this->_sessionModel = $sessionModel;
        parent::__construct($context, $data);
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
        return $this->_sessionModel->getCustomer();
    }
}
