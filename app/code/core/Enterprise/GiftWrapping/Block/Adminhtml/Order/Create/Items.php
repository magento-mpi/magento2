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
 * @package     Enterprise_GiftWrapping
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Gift wrapping order create items info block
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftWrapping_Block_Adminhtml_Order_Create_Items
    extends Enterprise_GiftWrapping_Block_Adminhtml_Order_Create_Abstract
{
    /**
     * Select element for choosing gift wrapping design
     *
     * @return array
     */
    public function getDesignSelectHtml()
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setData(array(
                'id'    => 'giftwrapping_design_item',
                'class' => 'select'
            ))
            ->setOptions($this->getDesignCollection()->toOptionArray());
        return $select->getHtml();
    }

    /**
     * Prepare and return quote items info
     *
     * @return Varien_Object
     */
    public function getItemsInfo()
    {
        $data = array();
        foreach ($this->getQuote()->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $allowed = $item->getProduct()->getGiftWrappingAvailable();
            if (Mage::helper('enterprise_giftwrapping')->isGiftWrappingAvailableForItems($allowed)) {
                $temp = array();
                if ($price = $item->getProduct()->getGiftWrappingPrice()) {
                    if ($this->getDisplayWrappingBothPrices()) {
                        $temp['price_incl_tax'] = $this->calculatePrice(new Varien_Object(), $price, true);
                        $temp['price_excl_tax'] = $this->calculatePrice(new Varien_Object(), $price);
                    } else {
                        $temp['price'] = $this->calculatePrice(new Varien_Object(), $price, $this->getDisplayWrappingPriceInclTax());
                    }
                }
                $temp['design'] = $item->getGwId();
                $data[$item->getId()] = $temp;
            }
        }
        return new Varien_Object($data);
    }

    /**
     * Retrieve wrapping design from current quote
     *
     * @return int
     */
    public function getWrappingDesignValue()
    {
        return (int)$this->getQuote()->getGwId();
    }

    /**
     * Retrieve wrapping gift receipt from current quote
     *
     * @return int
     */
    public function getWrappingGiftReceiptValue()
    {
        return (int)$this->getQuote()->getGwAllowGiftReceipt();
    }

    /**
     * Retrieve wrapping printed card from current quote
     *
     * @return int
     */
    public function getWrappingPrintedCardValue()
    {
        return (int)$this->getQuote()->getGwAddPrintedCard();
    }
    /**
     * Check ability to display both prices for printed card in shopping cart
     *
     * @return bool
     */
    public function getDisplayCardBothPrices()
    {
        return Mage::helper('enterprise_giftwrapping')->displayCartCardBothPrices($this->getStoreId());
    }

    /**
     * Check ability to display prices including tax for printed card in shopping cart
     *
     * @return bool
     */
    public function getDisplayCardPriceInclTax()
    {
        return Mage::helper('enterprise_giftwrapping')->displayCartCardIncludeTaxPrice($this->getStoreId());
    }

    /**
     * Check allow printed card
     *
     * @return bool
     */
    public function getAllowPrintedCard()
    {
        return Mage::helper('enterprise_giftwrapping')->allowPrintedCard($this->getStoreId());
    }

    /**
     * Check allow gift receipt
     *
     * @return bool
     */
    public function getAllowGiftReceipt()
    {
        return Mage::helper('enterprise_giftwrapping')->allowGiftReceipt($this->getStoreId());
    }
}
