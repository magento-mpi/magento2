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
     * @var \Magento\Multishipping\Model\Checkout\Type\MultishippingFactory
     */
    protected $typeMultiShippingFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param \Magento\Checkout\Model\Session $customerSession
     * @param \Magento\Multishipping\Model\Checkout\Type\MultishippingFactory $typeMultiShippingFactory
     * @param \Magento\GiftRegistry\Model\EntityFactory $entityFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        \Magento\Checkout\Model\Session $customerSession,
        \Magento\Multishipping\Model\Checkout\Type\MultishippingFactory $typeMultiShippingFactory,
        \Magento\GiftRegistry\Model\EntityFactory $entityFactory,
        array $data = array()
    ) {
        $this->typeMultiShippingFactory = $typeMultiShippingFactory;
        parent::__construct($context, $giftRegistryData, $customerSession, $entityFactory, $data);
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
        $quoteAddressItems = $this->typeMultiShippingFactory->create()->getQuoteShippingAddressesItems();
        foreach ($quoteAddressItems as $index => $quoteAddressItem) {
            $quoteItemId = $quoteAddressItem->getQuoteItem()->getId();
            if (!$quoteAddressItem->getCustomerAddressId() && in_array($quoteItemId, $registryQuoteItemIds)) {
                $result[] = $index;
            }
        }
        return $result;
    }
}
