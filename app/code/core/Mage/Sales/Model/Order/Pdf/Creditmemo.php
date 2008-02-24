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
            $image = Mage::getStoreConfig('sales/identity/main_image');
            if ($image) {
                $image = Mage::getStoreConfig('system/filesystem/media') . '/' . $image;
                $image = Zend_Pdf_Image::imageWithPath($image);
                $page->drawImage($image, 25, 800, 125, 825);
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
                    $page->drawText($value, 130, $y);
                    $y -=7;
                }
            }

            /* Add head */
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.5));

            $page->drawRectangle(25, 790, 570, 755);


            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 7);
            $page->drawText('Order # '.$order->getRealOrderId(), 35, 780);

            $page->drawText('Order Date: ' . date( 'D M j Y', strtotime( $order->getCreatedAt() ) ), 35, 770);
            $page->drawText('Creditmemo # ' . $creditmemo->getIncrementId(), 35, 760);

            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, 755, 275, 730);
            $page->drawRectangle(275, 755, 570, 730);

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 7);
            $page->drawText('SOLD TO:', 35, 740 );
            $page->drawText('SHIP TO:', 285, 740 );

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->drawRectangle(25, 730, 570, 665);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 7);

            $y = 720;

            foreach (explode('|', $order->getBillingAddress()->format('pdf')) as $value){
                if ($value!=='') {
                    $page->drawText($value, 35, $y);
                    $y -=10;
                }
            }

            $y = 720;
            foreach (explode('|', $order->getShippingAddress()->format('pdf')) as $value){
                if ($value!=='') {
                    $page->drawText($value, 285, $y);
                    $y -=10;
                }

            }

            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, 665, 275, 640);
            $page->drawRectangle(275, 665, 570, 640);

            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 7);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText('Payment Method', 35, 650);
            $page->drawText('Shipping Method:', 285, 650 );

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->drawRectangle(25, 640, 570, 615);

            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 7);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText(Mage::helper('payment')->getInfoBlock($order->getPayment())->toHtml(), 35, 625);
            $page->drawText($order->getShippingDescription(), 285, 625);

            $y = 600;

            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $y, 570, $y-15);
            $y -=10;

            /* Add table head */
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
            $page->drawText('QTY', 35, $y);
            $page->drawText('Products', 60, $y);
            $page->drawText('Tax', 280, $y);
            $page->drawText('Discount', 330, $y);
            $page->drawText('Price(ex)', 380, $y);
            $page->drawText('Price(inc)', 430, $y);
            $page->drawText('Total(ex)', 480, $y);
            $page->drawText('Total(inc)', 530, $y);

            $y -=15;

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            /* Add body */
            foreach ($creditmemo->getAllItems() as $item){
                if ($y<15) {
                    /* Add new table head */
                    $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
                    $pdf->pages[] = $page;
                    $y = 800;

                    $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 7);
                    $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
                    $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                    $page->setLineWidth(0.5);
                    $page->drawRectangle(25, $y, 660, $y-15);
                    $y -=10;

                    $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
                    $page->drawText('QTY', 35, $y);
                    $page->drawText('Products', 60, $y);
                    $page->drawText('Tax', 280, $y);
                    $page->drawText('Discount', 330, $y);
                    $page->drawText('Price(ex)', 380, $y);
                    $page->drawText('Price(inc)', 430, $y);
                    $page->drawText('Total(ex)', 480, $y);
                    $page->drawText('Total(inc)', 530, $y);

                    $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                    $y -=20;
                }

                /* Add products */
                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 7);
                $page->drawText($item->getQty()*1, 35, $y);
                $page->drawText($item->getName(), 60, $y);

                $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 7);
                $page->drawText($order->formatPrice($item->getTaxAmount()), 280, $y);
                $page->drawText($order->formatPrice(-$item->getDiscountAmount()), 330, $y);
                $page->drawText($order->formatPrice($item->getPrice()), 380, $y);
                $page->drawText($order->formatPrice($item->getPrice()+$item->getTaxAmount()), 430, $y);
                $page->drawText($order->formatPrice($item->getRowTotal()), 480, $y);
                $page->drawText($order->formatPrice($item->getRowTotal()+$item->getTaxAmount()-$item->getDiscountAmount()), 530, $y);
                $y -=20;
            }
        }
        return $pdf;
    }

}