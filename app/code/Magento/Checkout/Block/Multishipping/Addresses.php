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
 * Multishipping checkout choose item addresses block
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Multishipping_Addresses extends Magento_Sales_Block_Items_Abstract
{
    /**
     * @var Magento_Filter_Object_GridFactory
     */
    protected $_filterGridFactory;

    /**
     * @var Magento_Checkout_Model_Type_Multishipping
     */
    protected $_multishipping;

    /**
     * @param Magento_Filter_Object_GridFactory $filterGridFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Checkout_Model_Type_Multishipping $multishipping
     * @param array $data
     */
    public function __construct(
        Magento_Filter_Object_GridFactory $filterGridFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Checkout_Model_Type_Multishipping $multishipping,
        array $data = array()
    ) {
        $this->_filterGridFactory = $filterGridFactory;
        $this->_multishipping = $multishipping;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve multishipping checkout model
     *
     * @return Magento_Checkout_Model_Type_Multishipping
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
        /** @var Magento_Filter_Object_Grid $itemsFilter */
        $itemsFilter = $this->_filterGridFactory->create();
        $itemsFilter->addFilter(new Magento_Filter_Sprintf('%d'), 'qty');
        return $itemsFilter->filter($items);
    }

    /**
     * Retrieve HTML for addresses dropdown
     *
     * @param $item
     * @param int $index
     * @return string
     */
    public function getAddressesHtmlSelect($item, $index)
    {
        $select = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
            ->setName('ship['.$index.']['.$item->getQuoteItemId().'][address]')
            ->setId('ship_'.$index.'_'.$item->getQuoteItemId().'_address')
            ->setValue($item->getCustomerAddressId())
            ->setOptions($this->getAddressOptions());

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
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }
            $this->setData('address_options', $options);
        }

        return $options;
    }

    /**
     * @return Magento_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->getCheckout()->getCustomerSession()->getCustomer();
    }

    /**
     * @param $item
     * @return string
     */
    public function getItemUrl($item)
    {
        return $this->getUrl('catalog/product/view/id/' . $item->getProductId());
    }

    /**
     * @param $item
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
        return $this->getUrl('*/multishipping_address/newShipping');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/cart/');
    }

    /**
     * @return bool
     */
    public function isContinueDisabled()
    {
        return !$this->getCheckout()->validateMinimumAmount();
    }
}
