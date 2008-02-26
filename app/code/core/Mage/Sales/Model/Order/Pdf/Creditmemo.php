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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment method abstract model
 *
 * @author Yuriy Scherbina <yuriy.scherbina@varien.com>
 */
class Mage_Sales_Model_Order_Pdf_Creditmemo extends Mage_Sales_Model_Order_Pdf_Abstract
{
    public function getPdf($creditmemos = array())
    {
        $pdf = new Zend_Pdf();
        $style = new Zend_Pdf_Style();
        $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);

        foreach ($creditmemos as $creditmemo) {
            $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $pdf->pages[] = $page;

            $order = $creditmemo->getOrder();

            /* Add image */
            $image = Mage::getStoreConfig('sales/identity/logo');
            if ($image) {
                if (is_file($image)) {
                    $image = Mage::getStoreConfig('system/filesystem/media') . '/' . $image;
                    $image = Zend_Pdf_Image::imageWithPath($image);
                    $page->drawImage($image, 25, 800, 125, 825);
                }
            }

            /* Add address */
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 5);

            $page->setLineWidth(0.5);
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->drawLine(125, 825, 125, 790);

            $page->setLineWidth(0);
            $y = 820;
            foreach (explode("\n", Mage::getStoreConfig('sales/identity/address')) as $value){
                if ($value!=='') {
                    $page->drawText(strip_tags($value), 130, $y);
                    $y -=7;
                }
            }

            /* Add head */
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.5));

            $page->drawRectangle(25, 790, 570, 755);

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 7);

            $page->drawText(Mage::helper('sales')->__('Creditmemo # ') . $creditmemo->getIncrementId(), 35, 780);
            $page->drawText(Mage::helper('sales')->__('Order # ').$order->getRealOrderId(), 35, 770);
            $page->drawText(Mage::helper('sales')->__('Order Date: ') . date( 'D M j Y', strtotime( $order->getCreatedAt() ) ), 35, 760);

            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, 755, 275, 730);
            $page->drawRectangle(275, 755, 570, 730);

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 7);
            $page->drawText(Mage::helper('sales')->__('SOLD TO:'), 35, 740 );
            $page->drawText(Mage::helper('sales')->__('SHIP TO:'), 285, 740 );

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->drawRectangle(25, 730, 570, 665);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 7);

            $y = 720;

            foreach (explode('|', $order->getBillingAddress()->format('pdf')) as $value){
                if ($value!=='') {
                    $page->drawText(strip_tags($value), 35, $y);
                    $y -=10;
                }
            }

            $y = 720;
            foreach (explode('|', $order->getShippingAddress()->format('pdf')) as $value){
                if ($value!=='') {
                    $page->drawText(strip_tags($value), 285, $y);
                    $y -=10;
                }

            }

            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $y, 275, $y-25);
            $page->drawRectangle(275, $y, 570, $y-25);

            $y -=15;
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 7);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText(Mage::helper('sales')->__('Payment Method'), 35, $y);
            $page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, $y );

            $y -=10;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $payment = explode('<br/>', Mage::helper('payment')->getInfoBlock($order->getPayment())->toHtml());
            foreach ($payment as $key=>$value){
                if (strip_tags(trim($value))==''){
                    unset($payment[$key]);
                }
            }
            reset($payment);

            $page->drawRectangle(25, $y, 570, $y-count($payment)*10-15);

            $y -=15;
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 7);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $page->drawText($order->getShippingDescription(), 285, $y);
            foreach ($payment as $value){
                if (trim($value)!=='') {
                    $page->drawText(strip_tags(trim($value)), 35, $y);
                    $y -=10;
                }
            }

            $y -= 15;
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $y, 570, $y-15);

            $y -=10;

            /* Add table head */
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
            $page->drawText(Mage::helper('sales')->__('QTY'), 35, $y);
            $page->drawText(Mage::helper('sales')->__('Products'), 60, $y);
            $page->drawText(Mage::helper('sales')->__('Tax'), 280, $y);
            $page->drawText(Mage::helper('sales')->__('Discount'), 330, $y);
            $page->drawText(Mage::helper('sales')->__('Price(ex)'), 380, $y);
            $page->drawText(Mage::helper('sales')->__('Price(inc)'), 430, $y);
            $page->drawText(Mage::helper('sales')->__('Total(ex)'), 480, $y);
            $page->drawText(Mage::helper('sales')->__('Total(inc)'), 530, $y);

            $y -=15;

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            /* Add body */
            foreach ($creditmemo->getAllItems() as $item){
                if ($y<20) {
                    /* Add new table head */
                    $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
                    $pdf->pages[] = $page;
                    $y = 800;

                    $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 7);
                    $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
                    $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                    $page->setLineWidth(0.5);
                    $page->drawRectangle(25, $y, 570, $y-15);
                    $y -=10;

                    $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
                    $page->drawText(Mage::helper('sales')->__('QTY'), 35, $y);
                    $page->drawText(Mage::helper('sales')->__('Products'), 60, $y);
                    $page->drawText(Mage::helper('sales')->__('Tax'), 280, $y);
                    $page->drawText(Mage::helper('sales')->__('Discount'), 330, $y);
                    $page->drawText(Mage::helper('sales')->__('Price(ex)'), 380, $y);
                    $page->drawText(Mage::helper('sales')->__('Price(inc)'), 430, $y);
                    $page->drawText(Mage::helper('sales')->__('Total(ex)'), 480, $y);
                    $page->drawText(Mage::helper('sales')->__('Total(inc)'), 530, $y);

                    $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                    $y -=20;
                }

                /* Add products */
                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 7);
                $page->drawText($item->getQty()*1, 35, $y);
                $page->drawText($item->getName() . '(' . $item->getSku() . ')', 60, $y);

                $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
                $page->setFont($font, 7);

                $page->drawText($order->formatPrice($item->getTaxAmount()), 280, $y);
                $page->drawText($order->formatPrice(-$item->getDiscountAmount()), 330, $y);
                $page->drawText($order->formatPrice($item->getPrice()), 380, $y);
                $page->drawText($order->formatPrice($item->getPrice()+$item->getTaxAmount()), 430, $y);
                $page->drawText($order->formatPrice($item->getRowTotal()), 480, $y);

                $row_total = $order->formatPrice($item->getRowTotal()+$item->getTaxAmount()-$item->getDiscountAmount());

                $page->drawText($row_total, 565-$this->widthForStringUsingFontSize($row_total, $font, 7), $y);

                $y -=20;
            }

            /* Add totals */
            if ($creditmemo->getSubtotal()!=$creditmemo->getGrandTotal()) {
                $page ->drawText(Mage::helper('sales')->__('Order Subtotal:'), 421, $y);

                $order_subtotal = $order->formatPrice($creditmemo->getSubtotal());
                $page ->drawText($order_subtotal, 565-$this->widthForStringUsingFontSize($order_subtotal, $font, 7), $y);
                $y -=15;
            }

            if ($creditmemo->getShippingAmount()){
                $page ->drawText(Mage::helper('sales')->__('Shipping:'), 440, $y);

                $order_shipping = $order->formatPrice($creditmemo->getShippingAmount());
                $page ->drawText($order_shipping, 565-$this->widthForStringUsingFontSize($order_shipping, $font, 7), $y);
                $y -=15;
            }

            if ($creditmemo->getAdjustmentPositive()){
                $page ->drawText(Mage::helper('sales')->__('Adjustment Refund:'), 406, $y);

                $adjustment_refund = $order->formatPrice($creditmemo->getAdjustmentPositive());
                $page ->drawText($adjustment_refund, 565-$this->widthForStringUsingFontSize($adjustment_refund, $font, 7), $y);
                $y -=15;
            }

            if ((float) $creditmemo->getAdjustmentNegative()){
                $page ->drawText(Mage::helper('sales')->__('Adjustment Fee:'), 417, $y);

                $adjustment_fee=$order->formatPrice($creditmemo->getAdjustmentNegative());
                $page ->drawText($adjustment_fee, 565-$this->widthForStringUsingFontSize($adjustment_fee, $font, 7), $y);
                $y -=15;
            }

            $page->setFont($font, 8);

            $page ->drawText(Mage::helper('sales')->__('Grand Total:'), 425, $y);

            $order_grandtotal = $order->formatPrice($creditmemo->getGrandTotal());
            $page ->drawText($order_grandtotal, 565-$this->widthForStringUsingFontSize($order_grandtotal, $font, 8), $y);
        }
        return $pdf;
    }

}