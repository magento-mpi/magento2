<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Block\Checkout;

/**
 * Multishipping checkout choose item addresses block
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Addresses extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * @var \Magento\Filter\Object\GridFactory
     */
    protected $_filterGridFactory;

    /**
     * @var \Magento\Multishipping\Model\Checkout\Type\Multishipping
     */
    protected $_multishipping;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Filter\Object\GridFactory $filterGridFactory
     * @param \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Filter\Object\GridFactory $filterGridFactory,
        \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping,
        array $data = array()
    ) {
        $this->_filterGridFactory = $filterGridFactory;
        $this->_multishipping = $multishipping;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve multishipping checkout model
     *
     * @return \Magento\Multishipping\Model\Checkout\Type\Multishipping
     */
    public function getCheckout()
    {
        return $this->_multishipping;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Ship to Multiple Addresses') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    public function getItems()
    {
        $items = $this->getCheckout()->getQuoteShippingAddressesItems();
        /** @var \Magento\Filter\Object\Grid $itemsFilter */
        $itemsFilter = $this->_filterGridFactory->create();
        $itemsFilter->addFilter(new \Magento\Filter\Sprintf('%d'), 'qty');
        return $itemsFilter->filter($items);
    }

    /**
     * Retrieve HTML for addresses dropdown
     *
     * @param mixed $item
     * @param int $index
     * @return string
     */
    public function getAddressesHtmlSelect($item, $index)
    {
        $select = $this->getLayout()->createBlock(
            'Magento\View\Element\Html\Select'
        )->setName(
            'ship[' . $index . '][' . $item->getQuoteItemId() . '][address]'
        )->setId(
            'ship_' . $index . '_' . $item->getQuoteItemId() . '_address'
        )->setValue(
            $item->getCustomerAddressId()
        )->setOptions(
            $this->getAddressOptions()
        );

        return $select->getHtml();
    }

    /**
     * Retrieve options for addresses dropdown
     *
     * @return array
     */
    public function getAddressOptions()
    {
        $options = $this->getData('address_options');
        if (is_null($options)) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array('value' => $address->getId(), 'label' => $address->format('oneline'));
            }
            $this->setData('address_options', $options);
        }

        return $options;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->getCheckout()->getCustomerSession()->getCustomer();
    }

    /**
     * @param mixed $item
     * @return string
     */
    public function getItemUrl($item)
    {
        return $this->getUrl('catalog/product/view/id/' . $item->getProductId());
    }

    /**
     * @param mixed $item
     * @return string
     */
    public function getItemDeleteUrl($item)
    {
        return $this->getUrl('*/*/removeItem', array('address' => $item->getQuoteAddressId(), 'id' => $item->getId()));
    }

    /**
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/addressesPost');
    }

    /**
     * @return string
     */
    public function getNewAddressUrl()
    {
        return $this->getUrl('*/checkout_address/newShipping');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('checkout/cart/');
    }

    /**
     * @return bool
     */
    public function isContinueDisabled()
    {
        return !$this->getCheckout()->validateMinimumAmount();
    }
}
