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
 * Sales Order Invoice Pdf default items renderer
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Items_Invoice_Default extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
    /**
     * Draw item line
     *
     */
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = array();

        // draw Product name
        $stringHelper = Mage::helper('Mage_Core_Helper_String');
        $lines[0] = array(array(
            'text' => $stringHelper->str_split($item->getName(), 60, true, true),
            'feed' => 35,
        ));

        // draw SKU
        $lines[0][] = array(
            'text'  => $stringHelper->str_split($this->getSku($item), 25),
            'feed'  => 255
        );

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty()*1,
            'feed'  => 435
        );

        // draw Price
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getPrice()),
            'feed'  => 395,
            'font'  => 'bold',
            'align' => 'right'
        );

        // draw Tax
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getTaxAmount()),
            'feed'  => 495,
            'font'  => 'bold',
            'align' => 'right'
        );

        // draw Subtotal
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getRowTotal()),
            'feed'  => 565,
            'font'  => 'bold',
            'align' => 'right'
        );

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => $stringHelper->str_split(strip_tags($option['label']), 70, true, true),
                    'font' => 'italic',
                    'feed' => 35
                );

                if ($option['value']) {
                    $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => $stringHelper->str_split($value, 50, true, true),
                            'feed' => 40
                        );
                    }
                }
            }
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 10
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
}
