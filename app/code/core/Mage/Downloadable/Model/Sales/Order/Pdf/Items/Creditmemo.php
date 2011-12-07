<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order Creditmemo Downloadable Pdf Items renderer
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Sales_Order_Pdf_Items_Creditmemo extends Mage_Downloadable_Model_Sales_Order_Pdf_Items_Abstract
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

        $leftBound  =  35;
        $rightBound = 565;

        $x = $leftBound;
        // draw Product name
        $stringHelper = Mage::helper('Mage_Core_Helper_String');
        $lines[0] = array(array(
            'text' => $stringHelper->str_split($item->getName(), 60, true, true),
            'feed' => $x,
        ));

        $x += 220;
        // draw SKU
        $lines[0][] = array(
            'text'  => $stringHelper->str_split($this->getSku($item), 25),
            'feed'  => $x
        );

        $x += 100;
        // draw Total (ex)
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getRowTotal()),
            'feed'  => $x,
            'font'  => 'bold',
            'align' => 'right',
            'width' => 50,
        );

        $x += 50;
        // draw Discount
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt(-$item->getDiscountAmount()),
            'feed'  => $x,
            'font'  => 'bold',
            'align' => 'right',
            'width' => 50,
        );

        $x += 50;
        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty()*1,
            'feed'  => $x,
            'font'  => 'bold',
            'align' => 'center',
            'width' => 30,
        );

        $x += 30;
        // draw Tax
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getTaxAmount()),
            'feed'  => $x,
            'font'  => 'bold',
            'align' => 'right',
            'width' => 45,
        );

        $x += 45;
        // draw Subtotal
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getRowTotal() + $item->getTaxAmount() - $item->getDiscountAmount()),
            'feed'  => $rightBound,
            'font'  => 'bold',
            'align' => 'right'
        );

        // draw options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => $stringHelper->str_split(strip_tags($option['label']), 70, true, true),
                    'font' => 'italic',
                    'feed' => $leftBound
                );

                // draw options value
                $_printValue = isset($option['print_value'])
                    ? $option['print_value']
                    : strip_tags($option['value']);
                $lines[][] = array(
                    'text' => $stringHelper->str_split($_printValue, 50, true, true),
                    'feed' => $leftBound + 5
                );
            }
        }

        // downloadable Items
        $_purchasedItems = $this->getLinks()->getPurchasedItems();

        // draw Links title
        $lines[][] = array(
            'text' => $stringHelper->str_split($this->getLinksTitle(), 70, true, true),
            'font' => 'italic',
            'feed' => 35
        );

        // draw Links
        foreach ($_purchasedItems as $_link) {
            $lines[][] = array(
                'text' => $stringHelper->str_split($_link->getLinkTitle(), 50, true, true),
                'feed' => 40
            );
        }

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 10
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
}
