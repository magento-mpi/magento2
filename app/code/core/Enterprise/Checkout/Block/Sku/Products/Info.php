<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * SKU failed information Block
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 *
 * @method Mage_Sales_Model_Quote_Item getItem()
 */
class Enterprise_Checkout_Block_Sku_Products_Info extends Mage_Core_Block_Template
{
    /**
     * Helper instance
     *
     * @var Enterprise_Checkout_Helper_Data|null
     */
    protected $_helper;

    /**
     * Retrieve helper instance
     *
     * @return Enterprise_Checkout_Helper_Data
     */
    protected function _getHelper()
    {
        if (is_null($this->_helper)) {
            $this->_helper = Mage::helper('enterprise_checkout');
        }
        return $this->_helper;
    }

    /**
     * Retrieve item's message
     *
     * @return string
     */
    public function getMessage()
    {
        switch ($this->getItem()->getCode()) {
            case Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_PERMISSIONS:
                return $this->_getHelper()->getMessage(
                    Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_PERMISSIONS
                );
            case Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK:
                $message = '<span class="sku-out-of-stock" id="sku-stock-failed-' . $this->getItem()->getId() . '">'
                    . $this->_getHelper()->getMessage(
                        Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK
                    ) . '</span>';
                return $message;
            case Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED:
                $message = $this->_getHelper()->getMessage(
                    Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED
                );
                $message .= '<br/>' . $this->__("Only %s%g%s left in stock", '<span class="sku-failed-qty" id="sku-stock-failed-' . $this->getItem()->getId() . '">', $this->getItem()->getQtyMaxAllowed(), '</span>');
                return $message;
            case Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART:
                $item = $this->getItem();
                $message = $this->_getHelper()->getMessage(
                    Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART
                );
                $message .= '<br/>';
                if ($item->getQtyMaxAllowed()) {
                    $message .= Mage::helper('cataloginventory')->__('The maximum quantity allowed for purchase is %s.', '<span class="sku-failed-qty" id="sku-stock-failed-' . $item->getId() . '">' . ($item->getQtyMaxAllowed()  * 1) . '</span>');
                } else if ($item->getQtyMinAllowed()) {
                    $message .= Mage::helper('cataloginventory')->__('The minimum quantity allowed for purchase is %s.', '<span class="sku-failed-qty" id="sku-stock-failed-' . $item->getId() . '">' . ($item->getQtyMinAllowed()  * 1) . '</span>');
                }
                return $message;
            case Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_CONFIGURE:
                return $this->_getHelper()->getMessage(
                    Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_CONFIGURE
                );
            case Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_SKU:
                return $this->_getHelper()->getMessage(
                    Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_SKU
                );
            case Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_UNKNOWN:
                return $this->escapeHtml($this->getItem()->getError());
            default:
                return '';
        }
    }

    /**
     * Check whether item is 'SKU failed'
     *
     * @return bool
     */
    public function isItemSkuFailed()
    {
        return $this->getItem()->getCode() ==  Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_SKU;
    }

    /**
     * Get not empty template only for failed items
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getItem()->getCode() ? parent::_toHtml() : '';
    }

    /**
     * Get configure/notification/other link
     *
     * @return string
     */
    public function getLink()
    {
        $item = $this->getItem();
        switch ($item->getCode()) {
            case Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_CONFIGURE:
                $link = $this->getUrl('checkout/cart/configureFailed', array(
                    'id'  => $item->getProductId(),
                    'qty' => $item->getQty(),
                    'sku' => $item->getSku()
                ));
                return '<a href="' . $link . '" class="configure-popup">'
                        . $this->__('Specify the product\'s options')
                        . '</a>';
            case Enterprise_Checkout_Helper_Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK:
                /** @var $helper Mage_ProductAlert_Helper_Data */
                $helper = Mage::helper('productalert')->setProduct($this->getItem()->getProduct());
                $signUpLabel = $this->escapeHtml($this->__('Get notified when back in stock'));
                return '<a href="'
                    . $this->escapeHtml($helper->getSaveUrl('stock'))
                    . '" title="' . $signUpLabel . '">' . $signUpLabel . '</a>';
            default:
                return '';
        }
    }
}
