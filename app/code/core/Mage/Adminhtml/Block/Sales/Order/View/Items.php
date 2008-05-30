<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml order items grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_View_Items extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * Initialize template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/view/items.phtml');
    }

    protected function _getInfoBlock()
    {
        $block = $this->getData('_info_block');
        if (is_null($block)) {
            $block = $this->getLayout()->createBlock('adminhtml/sales_order_view_items_info');
            $this->setData('_info_block', $block);
        }
        return $block;
    }

    /**
     * REtrieve order instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('sales_order');
    }

    /**
     * Retrieve order items collection
     *
     * @return unknown
     */
    public function getItemsCollection()
    {
        return $this->getOrder()->getItemsCollection();
    }

    /**
     * Retrieve HTML for information column
     *
     * @param   Mage_Sales_Model_Order_Item $item
     * @return  string
     */
    public function renderInfoColumn($item)
    {
        $html = $this->_getInfoBlock()
            ->setEntity($item)
            ->toHtml();
        return $html;
    }

    protected function _getQtyBlock()
    {
        $block = $this->getData('_qty_block');
        if (is_null($block)) {
            $block = $this->getLayout()->createBlock('adminhtml/sales_order_item_qty');
            $this->setData('_qty_block', $block);
        }
        return $block;
    }

    public function getQtyHtml($item)
    {
        $html = $this->_getQtyBlock()
            ->setItem($item)
            ->toHtml();
        return $html;
    }

    public function displayTaxCalculation($item)
    {
        if ($item->getTaxPercent() && $item->getTaxString() == '') {
            $percents = array($item->getTaxPercent());
        } else if ($item->getTaxString()) {
            $percents = explode(Mage_Tax_Model_Config::CALCULATION_STRING_SEPARATOR, $item->getTaxString());
        } else {
            return '0%';
        }

        foreach ($percents as &$percent) {
            $percent = sprintf('%.2f%%', $percent);
        }
        return implode(' + ', $percents);
    }

    public function displayTaxPercent($item)
    {
        if ($item->getTaxPercent()) {
            return sprintf('%.2f%%', $item->getTaxPercent());
        } else {
            return '0%';
        }
    }

    public function displayPriceInclTaxColumn()
    {
        if (is_null($this->getData('_displayTaxColumn'))) {
            $this->setData('_displayTaxColumn', false);
            foreach ($this->getOrder()->getAllItems() as $item) {
                if ($item->getTaxAmount()) {
                    $this->setData('_displayTaxColumn', true);
                    break;
                }
            }
            if (!Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DISPLAY_TAX_COLUMN)) {
                $this->setData('_displayTaxColumn', false);
            }
        }
        return $this->getData('_displayTaxColumn');
    }

    public function displayPriceInclTax($item)
    {
        return $this->getOrder()->formatPrice($item->getPrice()+$item->getTaxAmount()/$item->getQtyOrdered());
    }
}