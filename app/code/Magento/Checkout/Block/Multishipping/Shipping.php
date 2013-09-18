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
 * Mustishipping checkout shipping
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Multishipping;

class Shipping extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * Get multishipping checkout model
     *
     * @return \Magento\Checkout\Model\Type\Multishipping
     */
    public function getCheckout()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Type\Multishipping');
    }

    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(__('Shipping Methods') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    public function getAddresses()
    {
        return $this->getCheckout()->getQuote()->getAllShippingAddresses();
    }

    public function getAddressCount()
    {
        $count = $this->getData('address_count');
        if (is_null($count)) {
            $count = count($this->getAddresses());
            $this->setData('address_count', $count);
        }
        return $count;
    }

    public function getAddressItems($address)
    {
        $items = array();
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            $item->setQuoteItem($this->getCheckout()->getQuote()->getItemById($item->getQuoteItemId()));
            $items[] = $item;
        }
        $itemsFilter = new \Magento\Filter\Object\Grid();
        $itemsFilter->addFilter(new \Magento\Filter\Sprintf('%d'), 'qty');
        return $itemsFilter->filter($items);
    }

    public function getAddressShippingMethod($address)
    {
        return $address->getShippingMethod();
    }

    public function getShippingRates($address)
    {
        $groups = $address->getGroupedAllShippingRates();
        return $groups;
    }

    public function getCarrierName($carrierCode)
    {
        if ($name = $this->_storeConfig->getConfig('carriers/'.$carrierCode.'/title')) {
            return $name;
        }
        return $carrierCode;
    }

    public function getAddressEditUrl($address)
    {
        return $this->getUrl('*/multishipping_address/editShipping', array('id'=>$address->getCustomerAddressId()));
    }

    public function getItemsEditUrl()
    {
        return $this->getUrl('*/*/backToAddresses');
    }

    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/shippingPost');
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/backtoaddresses');
    }

    public function getShippingPrice($address, $price, $flag)
    {
        return $address->getQuote()->getStore()->convertPrice($this->helper('Magento\Tax\Helper\Data')->getShippingPrice($price, $flag, $address), true);
    }
}
