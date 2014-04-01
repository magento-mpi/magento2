<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Customer\Checkout;

/**
 * Customer gift registry multishipping checkout block
 */
class Multishipping extends \Magento\GiftRegistry\Block\Customer\Checkout
{
    /**
     * @var \Magento\Multishipping\Model\Checkout\Type\Multishipping
     */
    protected $typeMultiShipping;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param \Magento\Checkout\Model\Session $customerSession
     * @param \Magento\GiftRegistry\Model\EntityFactory $entityFactory
     * @param \Magento\Multishipping\Model\Checkout\Type\Multishipping $typeMultiShipping
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        \Magento\Checkout\Model\Session $customerSession,
        \Magento\GiftRegistry\Model\EntityFactory $entityFactory,
        \Magento\Multishipping\Model\Checkout\Type\Multishipping $typeMultiShipping,
        array $data = array()
    ) {
        $this->typeMultiShipping = $typeMultiShipping;
        parent::__construct($context, $giftRegistryData, $customerSession, $entityFactory, $data);
    }

    /**
     * Get quote gift registry items
     *
     * @return array
     */
    public function getItems()
    {
        $items = array();
        foreach ($this->_getGiftRegistryQuoteItems() as $quoteItemId => $item) {
            if ($item['is_address']) {
                $items[$quoteItemId] = $item;
            }
        }
        return $items;
    }

    /**
     * Retrieve giftregistry selected addresses indexes
     *
     * @return array
     */
    public function getGiftregistrySelectedAddressesIndexes()
    {
        $result = array();
        $registryQuoteItemIds = array_keys($this->getItems());
        $quoteAddressItems = $this->typeMultiShipping->getQuoteShippingAddressesItems();
        foreach ($quoteAddressItems as $index => $quoteAddressItem) {
            $quoteItemId = $quoteAddressItem->getQuoteItem()->getId();
            if (!$quoteAddressItem->getCustomerAddressId() && in_array($quoteItemId, $registryQuoteItemIds)) {
                $result[] = $index;
            }
        }
        return $result;
    }
}
