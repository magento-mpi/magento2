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
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Order Shipment Pdf items renderer
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Sales_Order_Pdf_Items_Shipment extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Abstract
{
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();

        $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 7);

        $shipItems = $this->getChilds($item);
        $items = array_merge(array($item->getOrderItem()), $item->getOrderItem()->getChildrenItems());

        $_prevOptionId = '';

        foreach ($items as $_item) {
            $shift  = array(0, 10, 0);

            $attributes = $this->getSelectionAttributes($_item);

            if ($_item->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
                    $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_ITALIC), 7);
                    $page->drawText($attributes['option_label'], 60, $pdf->y, 'UTF-8');
                    $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 7);
                    $_prevOptionId = $attributes['option_id'];
                    $pdf->y -= 10;
                }
            }

            if (($this->isShipmentSeparately() && $_item->getParentItem()) || (!$this->isShipmentSeparately() && !$_item->getParentItem())) {
                if (isset($shipItems[$_item->getId()])) {
                    $qty = $shipItems[$_item->getId()]->getQty()*1;
                } else if ($_item->getIsVirtual()) {
                    $qty = Mage::helper('bundle')->__('N/A');
                } else {
                    $qty = 0;
                }
            } else {
                $qty = '';
            }

            $page->drawText($qty, 35, $pdf->y, 'UTF-8');

            if ($_item->getParentItem()) {
                $feed = 65;
                $name = $this->getValueHtml($_item);
            } else {
                $feed = 60;
                $name = $_item->getName();
            }
            foreach (Mage::helper('core/string')->str_split($name, 80, true, true) as $key => $part) {
                $page->drawText($part, $feed, $pdf->y-$shift[0], 'UTF-8');
                if ($key > 0) {
                    $shift[0] += 10;
                }
            }

            foreach (Mage::helper('core/string')->str_split($item->getSku(), 36) as $key => $part) {
                $page->drawText($part, 440, $pdf->y-$shift[2], 'UTF-8');
                if ($key > 0) {
                    $shift[2] += 10;
                }
            }

            $pdf->y -=max($shift)+10;
        }

        if ($item->getOrderItem()->getProductOptions() || $item->getOrderItem()->getDescription()) {
            $shift[1] = 10;
            $options = $item->getOrderItem()->getProductOptions();
            if (isset($options['options'])) {
                foreach ($options['options'] as $option) {
                    $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_ITALIC), 7);
                    foreach (Mage::helper('core/string')->str_split(strip_tags($option['label']), 80) as $_option) {
                        $page->drawText($_option, 60, $pdf->y-$shift[1], 'UTF-8');
                        $shift[1] += 10;
                    }

                    $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 7);

                    if ($option['value']) {
                        $values = explode(', ', strip_tags($option['value']));
                        foreach ($values as $value) {
                            foreach (Mage::helper('core/string')->str_split($value, 80) as $_value) {
                                $page->drawText($_value, 65, $pdf->y-$shift[1], 'UTF-8');
                                $shift[1] += 10;
                            }
                        }
                    }
                }
            }

            foreach ($this->_parseDescription() as $description){
                $page->drawText(strip_tags($description), 65, $pdf->y-$shift{1}, 'UTF-8');
                $shift{1} += 10;
            }

            $pdf->y -= max($shift)+10;
        }
    }
}