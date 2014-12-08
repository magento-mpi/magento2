<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Customer;

/**
 * Customer gift registry view items block
 */
class Items extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Gift registry item factory
     *
     * @var \Magento\GiftRegistry\Model\ItemFactory
     */
    protected $itemFactory = null;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\GiftRegistry\Model\ItemFactory $itemFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\GiftRegistry\Model\ItemFactory $itemFactory,
        array $data = []
    ) {
        $this->_coreData = $coreData;
        $this->itemFactory = $itemFactory;
        parent::__construct(
            $context,
            $data
        );
        $this->_isScopePrivate = true;
    }

    /**
     * Return gift registry form header
     *
     * @return string
     */
    public function getFormHeader()
    {
        return __('View Gift Registry %1', $this->getEntity()->getTitle());
    }

    /**
     * Return list of gift registries
     *
     * @return \Magento\GiftRegistry\Model\Resource\Item\Collection
     */
    public function getItemCollection()
    {
        if (!$this->hasItemCollection()) {
            $collection = $this->itemFactory->create()->getCollection()->addRegistryFilter(
                $this->getEntity()->getId()
            );
            $this->setData('item_collection', $collection);
        }
        return $this->_getData('item_collection');
    }

    /**
     * Retrieve item formatted date
     *
     * @param \Magento\GiftRegistry\Model\Item $item
     * @return string
     */
    public function getFormattedDate($item)
    {
        return $this->formatDate($item->getAddedAt(), \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM);
    }

    /**
     * Retrieve escaped item note
     *
     * @param \Magento\GiftRegistry\Model\Item $item
     * @return string
     */
    public function getEscapedNote($item)
    {
        return $this->escapeHtml($item->getData('note'));
    }

    /**
     * Retrieve item qty
     *
     * @param \Magento\GiftRegistry\Model\Item $item
     * @return string
     */
    public function getItemQty($item)
    {
        return $item->getQty() * 1;
    }

    /**
     * Retrieve item fulfilled qty
     *
     * @param \Magento\GiftRegistry\Model\Item $item
     * @return string
     */
    public function getItemQtyFulfilled($item)
    {
        return $item->getQtyFulfilled() * 1;
    }

    /**
     * Return action form url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/updateItems', ['_current' => true]);
    }

    /**
     * Return back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('giftregistry');
    }

    /**
     * Return back url to search result page
     *
     * @return string
     */
    public function getSearchBackUrl()
    {
        return $this->getUrl('*/search/results');
    }

    /**
     * Returns product price
     *
     * @param \Magento\GiftRegistry\Model\Item $item
     * @return float|string
     */
    public function getPrice($item)
    {
        $product = $item->getProduct();
        $product->setCustomOptions($item->getOptionsByCode());
        return $this->_coreData->currency($product->getFinalPrice(), true, true);
    }
}
