<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multishipping checkout select billing address
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Multishipping\Address;

class Select extends \Magento\Checkout\Block\Multishipping\AbstractMultishipping
{
    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(__('Change Billing Address') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    public function getAddressCollection()
    {
        $collection = $this->getData('address_collection');
        if (is_null($collection)) {
            $collection = $this->_multishipping->getCustomer()->getAddresses();
            $this->setData('address_collection', $collection);
        }
        return $collection;
    }
    
    public function isAddressDefaultBilling($address)
    {
        return $address->getId() == $this->_multishipping->getCustomer()->getDefaultBilling();
    }
    
    public function isAddressDefaultShipping($address)
    {
        return $address->getId() == $this->_multishipping->getCustomer()->getDefaultShipping();
    }
    
    public function getEditAddressUrl($address)
    {
        return $this->getUrl('*/*/editAddress', array('id'=>$address->getId()));
    }

    public function getSetAddressUrl($address)
    {
        return $this->getUrl('*/*/setBilling', array('id'=>$address->getId()));
    }

    public function getAddNewUrl()
    {
        return $this->getUrl('*/*/newBilling');
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/multishipping/billing');
    }
}
