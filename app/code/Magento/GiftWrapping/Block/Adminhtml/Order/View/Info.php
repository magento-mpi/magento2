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
            $wrapping = \Mage::getModel('Magento\GiftWrapping\Model\Wrapping')->load($order->getGwId());
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
        return \Mage::helper('Magento\GiftWrapping\Helper\Data')->allowPrintedCard($this->getStoreId());
    }

    /**
     * Check allow gift receipt
     *
     * @return bool
     */
    public function getAllowGiftReceipt()
    {
        return \Mage::helper('Magento\GiftWrapping\Helper\Data')->allowGiftReceipt($this->getStoreId());
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
