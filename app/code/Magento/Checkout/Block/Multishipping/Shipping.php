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
     * @var \Magento\Filter\Object\GridFactory
     */
    protected $_filterGridFactory;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Filter\Object\GridFactory $filterGridFactory
     * @param \Magento\Checkout\Model\Type\Multishipping $multishipping
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Filter\Object\GridFactory $filterGridFactory,
        \Magento\Checkout\Model\Type\Multishipping $multishipping,
        \Magento\Tax\Helper\Data $taxHelper,
        array $data = array()
    ) {
        $this->_taxHelper = $taxHelper;
        $this->_filterGridFactory = $filterGridFactory;
        $this->_multishipping = $multishipping;
        parent::__construct($context, $data);
    }

    /**
     * Get multishipping checkout model
     *
     * @return \Magento\Checkout\Model\Type\Multishipping
     */
    public function getCheckout()
    {
        return $this->_multishipping;
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
        return $address->getQuote()->getStore()->convertPrice($this->_taxHelper->getShippingPrice($price, $flag, $address), true);
    }
}
