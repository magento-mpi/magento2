<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
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
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param \Magento\Catalog\Helper\Product\Compare $compareProduct
     * @param \Magento\Theme\Helper\Layout $layoutHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\GiftRegistry\Model\ItemFactory $itemFactory
     * @param array $data
     * @param array $priceBlockTypes
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Core\Model\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Math\Random $mathRandom,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Catalog\Helper\Product\Compare $compareProduct,
        \Magento\Theme\Helper\Layout $layoutHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Core\Helper\Data $coreData,
        \Magento\GiftRegistry\Model\ItemFactory $itemFactory,
        array $data = array(),
        array $priceBlockTypes = array()
    ) {
        $this->_coreData = $coreData;
        $this->itemFactory = $itemFactory;
        parent::__construct(
            $context,
            $catalogConfig,
            $registry,
            $taxData,
            $catalogData,
            $mathRandom,
            $cartHelper,
            $wishlistHelper,
            $compareProduct,
            $layoutHelper,
            $imageHelper,
            $data,
            $priceBlockTypes
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
            $collection = $this->itemFactory->create()->getCollection()
                ->addRegistryFilter($this->getEntity()->getId());
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
        return $this->formatDate($item->getAddedAt(), \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM);
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
        return $item->getQty()*1;
    }

    /**
     * Retrieve item fulfilled qty
     *
     * @param \Magento\GiftRegistry\Model\Item $item
     * @return string
     */
    public function getItemQtyFulfilled($item)
    {
        return $item->getQtyFulfilled()*1;
    }

    /**
     * Return action form url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/updateItems', array('_current' => true));
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
