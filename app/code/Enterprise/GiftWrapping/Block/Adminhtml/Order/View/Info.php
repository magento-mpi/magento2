<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping order view block
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftWrapping_Block_Adminhtml_Order_View_Info
    extends Enterprise_GiftWrapping_Block_Adminhtml_Order_View_Abstract
{
    public function __construct(Enterprise_GiftWrapping_Helper_Data $giftWrappingData, Magento_Core_Helper_Data $coreData, Magento_Core_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($giftWrappingData, $coreData, $context, $data);
    }

    /**
     * Prepare and return order items info
     *
     * @return Magento_Object
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
            $wrapping = Mage::getModel('Enterprise_GiftWrapping_Model_Wrapping')->load($order->getGwId());
            $data['path'] = $wrapping->getImageUrl();
            $data['design'] = $wrapping->getDesign();
        }
        return new Magento_Object($data);
    }

    /**
     * Prepare and return order items info
     *
     * @return Magento_Object
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
        return new Magento_Object($data);
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
