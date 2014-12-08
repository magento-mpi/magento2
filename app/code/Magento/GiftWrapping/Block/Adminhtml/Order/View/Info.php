<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping order view block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Order\View;

class Info extends \Magento\GiftWrapping\Block\Adminhtml\Order\View\AbstractView
{
    /**
     * @var \Magento\GiftWrapping\Model\WrappingFactory
     */
    protected $_wrappingFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollectionFactory
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        \Magento\Framework\Registry $registry,
        \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollectionFactory,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory,
        array $data = []
    ) {
        $this->_wrappingFactory = $wrappingFactory;
        parent::__construct($context, $giftWrappingData, $registry, $wrappingCollectionFactory, $adminHelper, $data);
    }

    /**
     * Prepare and return order items info
     *
     * @return \Magento\Framework\Object
     */
    public function getOrderInfo()
    {
        $data = [];
        $order = $this->getOrder();
        if ($order && $order->getGwId()) {
            if ($this->getDisplayWrappingBothPrices()) {
                $data['price_excl_tax'] = $this->_preparePrices($order->getGwBasePrice(), $order->getGwPrice());
                $data['price_incl_tax'] = $this->_preparePrices(
                    $order->getGwBasePrice() + $order->getGwBaseTaxAmount(),
                    $order->getGwPrice() + $order->getGwTaxAmount()
                );
            } elseif ($this->getDisplayWrappingPriceInclTax()) {
                $data['price'] = $this->_preparePrices(
                    $order->getGwBasePrice() + $order->getGwBaseTaxAmount(),
                    $order->getGwPrice() + $order->getGwTaxAmount()
                );
            } else {
                $data['price'] = $this->_preparePrices($order->getGwBasePrice(), $order->getGwPrice());
            }
            $wrapping = $this->_wrappingFactory->create()->load($order->getGwId());
            $data['path'] = $wrapping->getImageUrl();
            $data['design'] = $wrapping->getDesign();
        }
        return new \Magento\Framework\Object($data);
    }

    /**
     * Prepare and return order items info
     *
     * @return \Magento\Framework\Object
     */
    public function getCardInfo()
    {
        $data = [];
        $order = $this->getOrder();
        if ($order && $order->getGwAddCard()) {
            if ($this->getDisplayCardBothPrices()) {
                $data['price_excl_tax'] = $this->_preparePrices(
                    $order->getGwCardBasePrice(),
                    $order->getGwCardPrice()
                );
                $data['price_incl_tax'] = $this->_preparePrices(
                    $order->getGwCardBasePrice() + $order->getGwCardBaseTaxAmount(),
                    $order->getGwCardPrice() + $order->getGwCardTaxAmount()
                );
            } elseif ($this->getDisplayCardPriceInclTax()) {
                $data['price'] = $this->_preparePrices(
                    $order->getGwCardBasePrice() + $order->getGwCardBaseTaxAmount(),
                    $order->getGwCardPrice() + $order->getGwCardTaxAmount()
                );
            } else {
                $data['price'] = $this->_preparePrices($order->getGwCardBasePrice(), $order->getGwCardPrice());
            }
        }
        return new \Magento\Framework\Object($data);
    }

    /**
     * Check using printed card for order
     *
     * @return bool
     */
    public function getPrintedCard()
    {
        return (bool)$this->getOrder()->getGwAddCard();
    }

    /**
     * Check allow gift receipt for order
     *
     * @return bool
     */
    public function getGiftReceipt()
    {
        return (bool)$this->getOrder()->getGwAllowGiftReceipt();
    }

    /**
     * Check allow printed card
     *
     * @return bool
     */
    public function getAllowPrintedCard()
    {
        return $this->_giftWrappingData->allowPrintedCard($this->getStoreId());
    }

    /**
     * Check allow gift receipt
     *
     * @return bool
     */
    public function getAllowGiftReceipt()
    {
        return $this->_giftWrappingData->allowGiftReceipt($this->getStoreId());
    }

    /**
     * Check allow gift wrapping for order
     *
     * @return bool
     */
    public function canDisplayGiftWrapping()
    {
        return $this->getOrder()->getGwId() || $this->getGiftReceipt() || $this->getPrintedCard();
    }
}
