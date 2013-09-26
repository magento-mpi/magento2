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
 * Gift wrapping order view block
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Order\View;

class Info
    extends \Magento\GiftWrapping\Block\Adminhtml\Order\View\AbstractView
{
    /**
     * @var \Magento\GiftWrapping\Model\WrappingFactory
     */
    protected $_wrappingFactory;

    /**
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollFactory
     * @param \Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory
     * @param array $data
     */
    public function __construct(
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollFactory,
        \Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory,
        array $data = array()
    ) {
        $this->_wrappingFactory = $wrappingFactory;
        parent::__construct(
            $giftWrappingData,
            $coreData,
            $context,
            $registry,
            $storeManager,
            $wrappingCollFactory,
            $data
        );
    }

    /**
     * Prepare and return order items info
     *
     * @return \Magento\Object
     */
    public function getOrderInfo()
    {
        $data = array();
        $order = $this->getOrder();
        if ($order && $order->getGwId()) {
            if ($this->getDisplayWrappingBothPrices()) {
                 $data['price_excl_tax'] = $this->_preparePrices($order->getGwBasePrice(), $order->getGwPrice());
                 $data['price_incl_tax'] = $this->_preparePrices(
                    $order->getGwBasePrice() + $order->getGwBaseTaxAmount(),
                    $order->getGwPrice() + $order->getGwTaxAmount()
                 );
            } else if ($this->getDisplayWrappingPriceInclTax()) {
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
        return new \Magento\Object($data);
    }

    /**
     * Prepare and return order items info
     *
     * @return \Magento\Object
     */
    public function getCardInfo()
    {
        $data = array();
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
            } else if ($this->getDisplayCardPriceInclTax()) {
                $data['price'] = $this->_preparePrices(
                    $order->getGwCardBasePrice() + $order->getGwCardBaseTaxAmount(),
                    $order->getGwCardPrice() + $order->getGwCardTaxAmount()
                );
            } else {
                $data['price'] = $this->_preparePrices(
                    $order->getGwCardBasePrice(),
                    $order->getGwCardPrice()
                );
            }
        }
        return new \Magento\Object($data);
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
