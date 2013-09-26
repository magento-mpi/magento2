<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping order view abstract block
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Order\View;

class AbstractView extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
     */
    protected $_designCollection;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Gift wrapping data
     *
     * @var \Magento\GiftWrapping\Helper\Data
     */
    protected $_giftWrappingData = null;

    /**
     * @var \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory
     */
    protected $_wrappingCollFactory;

    /**
     * Store list manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollFactory
     * @param array $data
     */
    public function __construct(
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_giftWrappingData = $giftWrappingData;
        $this->_storeManager = $storeManager;
        $this->_wrappingCollFactory = $wrappingCollFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('sales_order');
    }

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getOrder()->getStoreId();
    }

    /**
     * Gift wrapping collection
     *
     * @return \Magento\GiftWrapping\Model\Resource\Wrapping\Collection
     */
    public function getDesignCollection()
    {
        if (is_null($this->_designCollection)) {
            $store = $this->_storeManager->getStore($this->getStoreId());
            $this->_designCollection = $this->_wrappingCollFactory->create()
                ->addStoreAttributesToResult($store->getId())
                ->applyStatusFilter()
                ->applyWebsiteFilter($store->getWebsiteId());
        }
        return $this->_designCollection;
    }

    /**
     * Return gift wrapping designs info
     *
     * @return \Magento\Object
     */
    public function getDesignsInfo()
    {
        $data = array();
        foreach ($this->getDesignCollection()->getItems() as $item) {
            $temp['path'] = $item->getImageUrl();
            $temp['design'] = $this->escapeHtml($item->getDesign());
            $data[$item->getId()] = $temp;
        }
        return new \Magento\Object($data);
    }

    /**
     * Prepare prices for display
     * @param float $basePrice
     * @param float $price
     * @return string
     */
    protected function _preparePrices($basePrice, $price)
    {
        return $this->helper('Magento\Adminhtml\Helper\Sales')->displayPrices($this->getOrder(), $basePrice, $price);
    }

    /**
     * Check ability to display both prices for gift wrapping in backend sales
     *
     * @return bool
     */
    public function getDisplayWrappingBothPrices()
    {
        return $this->_giftWrappingData->displaySalesWrappingBothPrices();
    }

    /**
     * Check ability to display prices including tax for gift wrapping in backend sales
     *
     * @return bool
     */
    public function getDisplayWrappingPriceInclTax()
    {
        return $this->_giftWrappingData->displaySalesWrappingIncludeTaxPrice();
    }

    /**
     * Check ability to display both prices for printed card in backend sales
     *
     * @return bool
     */
    public function getDisplayCardBothPrices()
    {
        return $this->_giftWrappingData->displaySalesCardBothPrices();
    }

    /**
     * Check ability to display prices including tax for printed card in backend sales
     *
     * @return bool
     */
    public function getDisplayCardPriceInclTax()
    {
        return $this->_giftWrappingData->displaySalesCardIncludeTaxPrice();
    }
}
