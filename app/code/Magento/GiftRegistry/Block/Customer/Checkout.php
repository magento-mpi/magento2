<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer gift registry checkout abstract block
 */
namespace Magento\GiftRegistry\Block\Customer;

class Checkout extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\GiftRegistry\Model\EntityFactory
     */
    protected $entityFactory;

    /**
     * Gift registry data
     *
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftRegistryData = null;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param \Magento\Checkout\Model\Session $customerSession
     * @param \Magento\GiftRegistry\Model\EntityFactory $entityFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        \Magento\Checkout\Model\Session $customerSession,
        \Magento\GiftRegistry\Model\EntityFactory $entityFactory,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        $this->customerSession = $customerSession;
        $this->entityFactory = $entityFactory;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Get current checkout session
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckoutSession()
    {
        return $this->customerSession;
    }

    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function getEnabled()
    {
        return  $this->_giftRegistryData->isEnabled();
    }

    /**
     * Get customer quote gift registry items
     *
     * @return array
     */
    protected function _getGiftRegistryQuoteItems()
    {
        $items = array();
        if ($this->_getCheckoutSession()->getQuoteId()) {
            $quote = $this->_getCheckoutSession()->getQuote();
            $model = $this->entityFactory->create();
            foreach ($quote->getItemsCollection() as $quoteItem) {
                $item = array();
                if ($registryItemId = $quoteItem->getGiftregistryItemId()) {
                    $model->loadByEntityItem($registryItemId);
                    $item['entity_id'] = $model->getId();
                    $item['item_id'] = $registryItemId;
                    $item['is_address'] = ($model->getShippingAddress()) ? 1 : 0;
                    $items[$quoteItem->getId()] = $item;
                }
            }
        }
        return $items;
    }

    /**
     * Get quote unique gift registry item for onepage checkout
     *
     * @return false|int
     */
    public function getItem()
    {
        $items = array();
        foreach ($this->_getGiftRegistryQuoteItems() as $registryItem) {
            $items[$registryItem['entity_id']] = $registryItem;
        }
        if (count($items) == 1) {
            $item = array_shift($items);
            if ($item['is_address']) {
                return $item['item_id'];
            }
        }
        return false;
    }

    /**
     * Get select shipping address id prefix
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getAddressIdPrefix()
    {
        return $this->_giftRegistryData->getAddressIdPrefix();
    }
}
