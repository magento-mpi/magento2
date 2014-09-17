<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Order\Create;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Gift wrapping order create abstract block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class AbstractCreate extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * @var \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
     */
    protected $_designCollection;

    /**
     * Gift wrapping data
     *
     * @var \Magento\GiftWrapping\Helper\Data
     */
    protected $_giftWrappingData;

    /**
     * @var \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory
     */
    protected $_wrappingCollectionFactory;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Core\Helper\Data $coreData,
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollectionFactory,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        $this->_giftWrappingData = $giftWrappingData;
        $this->_wrappingCollectionFactory = $wrappingCollectionFactory;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * Gift wrapping collection
     *
     * @return \Magento\GiftWrapping\Model\Resource\Wrapping\Collection
     */
    public function getDesignCollection()
    {
        if (is_null($this->_designCollection)) {
            $this->_designCollection = $this->_wrappingCollectionFactory->create()->addStoreAttributesToResult(
                $this->getStore()->getId()
            )->applyStatusFilter()->applyWebsiteFilter(
                $this->getStore()->getWebsiteId()
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
        $data = array();
        foreach ($this->getDesignCollection()->getItems() as $item) {
            if ($this->getDisplayWrappingBothPrices()) {
                $temp['price_incl_tax'] = $this->calculatePrice($item, $item->getBasePrice(), true);
                $temp['price_excl_tax'] = $this->calculatePrice($item, $item->getBasePrice());
            } else {
                $temp['price'] = $this->calculatePrice(
                    $item,
                    $item->getBasePrice(),
                    $this->getDisplayWrappingPriceInclTax()
                );
            }
            $temp['path'] = $item->getImageUrl();
            $temp['design'] = $item->getDesign();
            $data[$item->getId()] = $temp;
        }
        return new \Magento\Framework\Object($data);
    }

    /**
     * Prepare and return printed card info
     *
     * @return \Magento\Framework\Object
     */
    public function getCardInfo()
    {
        $data = array();
        if ($this->getAllowPrintedCard()) {
            $price = $this->_giftWrappingData->getPrintedCardPrice($this->getStoreId());
            if ($this->getDisplayCardBothPrices()) {
                $data['price_incl_tax'] = $this->calculatePrice(new \Magento\Framework\Object(), $price, true);
                $data['price_excl_tax'] = $this->calculatePrice(new \Magento\Framework\Object(), $price);
            } else {
                $data['price'] = $this->calculatePrice(
                    new \Magento\Framework\Object(),
                    $price,
                    $this->getDisplayCardPriceInclTax()
                );
            }
        }
        return new \Magento\Framework\Object($data);
    }

    /**
     * Calculate price
     *
     * @param \Magento\Framework\Object $item
     * @param float $basePrice
     * @param bool $includeTax
     * @return string
     */
    public function calculatePrice($item, $basePrice, $includeTax = false)
    {
        $shippingAddress = $this->getQuote()->getShippingAddress();
        $billingAddress = $this->getQuote()->getBillingAddress();

        $taxClass = $this->_giftWrappingData->getWrappingTaxClass($this->getStoreId());
        $item->setTaxClassId($taxClass);

        $price = $this->_giftWrappingData->getPrice($item, $basePrice, $includeTax, $shippingAddress, $billingAddress);
        return $this->_coreData->currency($price, true, false);
    }

    /**
     * Check ability to display both prices for gift wrapping in shopping cart
     *
     * @return bool
     */
    public function getDisplayWrappingBothPrices()
    {
        return $this->_giftWrappingData->displayCartWrappingBothPrices($this->getStoreId());
    }

    /**
     * Check ability to display prices including tax for gift wrapping in shopping cart
     *
     * @return bool
     */
    public function getDisplayWrappingPriceInclTax()
    {
        return $this->_giftWrappingData->displayCartWrappingIncludeTaxPrice($this->getStoreId());
    }

    /**
     * Return quote id
     *
     * @return array|null
     */
    public function getEntityId()
    {
        return $this->getQuote()->getId();
    }
}
