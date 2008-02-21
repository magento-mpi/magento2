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
 * Adminhtml sales invoices block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Invoice extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'sales_invoice';
        $this->_headerText = Mage::helper('sales')->__('Invoices');
        parent::__construct();
        $this->_removeButton('add');
    }

    public function toPdf()
    {

        $orderIds = array($this->getRequest()->getParam('order_id'));

        $pdf = new Zend_Pdf();
        //        $pdf->AddPage();
        $style = new Zend_Pdf_Style();

        $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 10);


        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);

            $invoices = Mage::getResourceModel('sales/order_invoice_collection')
                ->addAttributeToSelect('*')
                ->setOrderFilter($orderId)
                ->load();

            $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $pdf->pages[] = $page;
            $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);


            if ($invoices) {


                //$image = Zend_Pdf_Image::imageWithPath($this->getBaseUrl().'img/logo-pdf-' . $order->getWebsitesId . '.png');
//                $page->drawImage($image, 20, 800, 100, 720);


//                $page->drawText($this->oApp->GetOSCConstantValue( 'STORE_NAME_ADDRESS', $order->getWebsitesId() ), 120, 800);
                $page->drawText(' Order # '.$order->getRealOrderId(), 20, 700);
                $page->drawText(' Order Date: ' . date( 'm-d-Y', strtotime( $order->getDatePurchased() ) ), 20, 680);
                //$page->drawText('RLB', 5, 5);


                $page->drawText(' SOLD TO:', 20, 640 );
                $page->drawText(' SHIP TO:', 220, 640 );


//                $page->drawText($order->getBillingAddress()->format('pdf'), 20, 620 );
//                $page->drawText($order->getShippingAddress()->format('pdf'), 20, 600 );
//var_dump($order->getBillingAddress());
                $y = 620;
                foreach (explode('|', $order->getBillingAddress()->format('pdf')) as $value){
                    if ($value!=='') {
                    	$page->drawText($value, 20, $y);
                        $y -=20;
                    }
                }

                $y = 620;
                foreach (explode('|', $order->getShippingAddress()->format('pdf')) as $value){
                    if ($value!=='') {
                    	$page->drawText($value, 220, $y);
                        $y -=20;
                    }

                }


                $page->drawText(' Payment Method', 20, 500);
                $page->drawText(Mage::helper('payment')->getInfoBlock($order->getPayment())->toHtml(), 20, 480);

                $page->drawText(' Shipping Method:', 220, 500 );
                $page->drawText($order->getShippingDescription(), 220, 480);



                foreach ($invoices as $invoice) {
                    $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
                    $pdf->pages[] = $page;
                    $page->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 10);
                    $page->drawText('Invoice # ' . $invoice->getIncrementId(), 20, 480);
                    $page->drawText('QTY', 20, 460);
                    $page->drawText('Products', 60, 460);
                    $page->drawText('Tax', 220, 460);
                    $page->drawText('Discount', 280, 460);
                    $page->drawText('Price(ex)', 340, 460);
                    $page->drawText('Price(inc)', 400, 460);
                    $page->drawText('Total(ex)', 460, 460);
                    $page->drawText('Total(inc)', 520, 460);

                    $y = 440;
                    foreach ($invoice->getAllItems() as $item){
                         $page->drawText($item->getQty()*1, 20, $y);
                         $page->drawText($item->getName(), 60, $y);
                         $page->drawText($order->formatPrice($item->getTaxAmount()), 220, $y);
                         $page->drawText($order->formatPrice(-$item->getDiscountAmount()), 280, $y);
                         $page->drawText($order->formatPrice($item->getPrice()), 340, $y);
                         $page->drawText($order->formatPrice($item->getPrice()+$item->getTaxAmount()), 400, $y);
                         $page->drawText($order->formatPrice($item->getRowTotal()), 460, $y);
                         $page->drawText($order->formatPrice($item->getRowTotal()+$item->getTaxAmount()-$item->getDiscountAmount()), 520, $y);
                         $y -=20;
                    }
                    $page ->drawText('Order Subtotal:', 400, 400);
                    $page ->drawText($order->formatPrice($invoice->getSubtotal()), 500, 400);

                    $page ->drawText('Shipping & Handling:', 400, 380);
                    $page ->drawText($order->formatPrice($invoice->getShippingAmount()), 500, 380);

                    $page ->drawText('Grand Total:', 400, 360);
                    $page ->drawText($order->formatPrice($invoice->getGrandTotal()), 500, 360);
                }

            }
            header('Content-Type: application/pdf');
            echo $pdf->render();
            die();
        }
    }

     public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }
}
