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
class Magento_Checkout_Block_Multishipping_Shipping extends Magento_Sales_Block_Items_Abstract
{
    /**
     * @var Magento_Filter_Object_GridFactory
     */
    protected $_filterGridFactory;

    /**
     * @param Magento_Filter_Object_GridFactory $filterGridFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Filter_Object_GridFactory $filterGridFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_filterGridFactory = $filterGridFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get multishipping checkout model
     *
     * @return Magento_Checkout_Model_Type_Multishipping
     */
    public function getCheckout()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Type_Multishipping');
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
        $itemsFilter = $this->_filterGridFactory->create();
        $itemsFilter->addFilter(new Magento_Filter_Sprintf('%d'), 'qty');
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
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title')) {
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
        return $address->getQuote()->getStore()->convertPrice($this->helper('Magento_Tax_Helper_Data')->getShippingPrice($price, $flag, $address), true);
    }
}
