<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Gift wrapping order view abstract block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Order\View;

class AbstractView extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
     */
    protected $_designCollection;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
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
    protected $_wrappingCollectionFactory;

    /**
     * @var \Magento\Sales\Helper\Admin
     */
    protected $_adminHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollectionFactory
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        \Magento\Framework\Registry $registry,
        \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollectionFactory,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        $this->_adminHelper = $adminHelper;
        $this->_coreRegistry = $registry;
        $this->_giftWrappingData = $giftWrappingData;
        $this->_wrappingCollectionFactory = $wrappingCollectionFactory;
        parent::__construct($context, $data);
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
            $this->_designCollection = $this->_wrappingCollectionFactory->create()->addStoreAttributesToResult(
                $store->getId()
            )->applyStatusFilter()->applyWebsiteFilter(
                $store->getWebsiteId()
            );
        }
        return $this->_designCollection;
    }

    /**
     * Return gift wrapping designs info
     *
     * @return \Magento\Framework\Object
     */
    public function getDesignsInfo()
    {
        $data = [];
        foreach ($this->getDesignCollection()->getItems() as $item) {
            $temp['path'] = $item->getImageUrl();
            $temp['design'] = $this->escapeHtml($item->getDesign());
            $data[$item->getId()] = $temp;
        }
        return new \Magento\Framework\Object($data);
    }

    /**
     * Prepare prices for display
     * @param float $basePrice
     * @param float $price
     * @return string
     */
    protected function _preparePrices($basePrice, $price)
    {
        return $this->_adminHelper->displayPrices($this->getOrder(), $basePrice, $price);
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
