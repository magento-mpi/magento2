<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales Order Creditmemo PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Creditmemo extends Mage_Sales_Model_Order_Pdf_Abstract
{
    public function getPdf($creditmemos = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('creditmemo');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($creditmemos as $creditmemo) {
            if ($creditmemo->getStoreId()) {
                Mage::app()->getLocale()->emulate($creditmemo->getStoreId());
                Mage::app()->setCurrentStore($creditmemo->getStoreId());
            }
            $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $pdf->pages[] = $page;

            $order = $creditmemo->getOrder();

            /* Add image */
            $this->insertLogo($page, $creditmemo->getStore());

            /* Add address */
            $this->insertAddress($page, $creditmemo->getStore());

            /* Add head */
            $this->insertOrder($page, $order, Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID, $order->getStoreId()));

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $this->_setFontRegular($page);
            $page->drawText(Mage::helper('Mage_Sales_Helper_Data')->__('Credit Memo # ') . $creditmemo->getIncrementId(), 35, 780, 'UTF-8');

            /* Add table head */
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y-15);
            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
            $this->_drawHeader($page);
            $this->y -=15;

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            /* Add body */
            foreach ($creditmemo->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }

                if ($this->y<20) {
                    $page = $this->newPage(array('table_header' => true));
                }

                /* Draw item */
                $page = $this->_drawItem($item, $page, $order);
            }

            /* Add totals */
            $page = $this->insertTotals($page, $creditmemo);
        }

        $this->_afterGetPdf();

        if ($creditmemo->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
    }

    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        $font = $page->getFont();
        $size = $page->getFontSize();

        $page->drawText(Mage::helper('Mage_Sales_Helper_Data')->__('Products'), $x = 35, $this->y, 'UTF-8');
        $x += 220;

        $page->drawText(Mage::helper('Mage_Sales_Helper_Data')->__('SKU'), $x, $this->y, 'UTF-8');
        $x += 100;

        $text = Mage::helper('Mage_Sales_Helper_Data')->__('Total (ex)');
        $page->drawText($text, $this->getAlignRight($text, $x, 50, $font, $size), $this->y, 'UTF-8');
        $x += 50;

        $text = Mage::helper('Mage_Sales_Helper_Data')->__('Discount');
        $page->drawText($text, $this->getAlignRight($text, $x, 50, $font, $size), $this->y, 'UTF-8');
        $x += 50;

        $text = Mage::helper('Mage_Sales_Helper_Data')->__('Qty');
        $page->drawText($text, $this->getAlignCenter($text, $x, 30, $font, $size), $this->y, 'UTF-8');
        $x += 30;

        $text = Mage::helper('Mage_Sales_Helper_Data')->__('Tax');
        $page->drawText($text, $this->getAlignRight($text, $x, 45, $font, $size, 10), $this->y, 'UTF-8');
        $x += 45;

        $text = Mage::helper('Mage_Sales_Helper_Data')->__('Total (inc)');
        $page->drawText($text, $this->getAlignRight($text, $x, 570 - $x, $font, $size), $this->y, 'UTF-8');
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        $page = parent::newPage($settings);

        if (!empty($settings['table_header'])) {
            $this->_setFontRegular($page);
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y-15);
            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
            $this->_drawHeader($page);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $this->y -=20;
        }

        return $page;
    }
}
