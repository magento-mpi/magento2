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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Order Shipment PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Shipment_Packaging extends Mage_Sales_Model_Order_Pdf_Abstract
{
    /**
     * Format pdf file
     *
     * @param null $shipment
     * @return Zend_Pdf
     */
    public function getPdf($shipment = null)
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new Zend_Pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $pdf->pages[] = $page;

        if ($shipment->getStoreId()) {
            Mage::app()->getLocale()->emulate($shipment->getStoreId());
            Mage::app()->setCurrentStore($shipment->getStoreId());
        }

        $this->_setFontRegular($page);
        $this->_drawHeaderBlock($page);

        $this->y = 740;
        $this->_drawPackageBlock($page);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_afterGetPdf();

        if ($shipment->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
    }

    /**
     * Draw header block
     *
     * @param Zend_Pdf_Page $page
     * @return Mage_Sales_Model_Order_Pdf_Shipment_Packaging
     */
    protected function _drawHeaderBlock(Zend_Pdf_Page $page) {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, 790, 570, 755);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawText(Mage::helper('sales')->__('Packages'), 35, 770, 'UTF-8');
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));

        return $this;
    }

    /**
     * Draw packages block
     *
     * @param Zend_Pdf_Page $page
     * @return Mage_Sales_Model_Order_Pdf_Shipment_Packaging
     */
    protected function _drawPackageBlock(Zend_Pdf_Page $page)
    {
        if ($this->getPackageShippingBlock()) {
            $packaging = $this->getPackageShippingBlock();
        } else {
            $packaging = Mage::getBlockSingleton('adminhtml/sales_order_shipment_packaging');
        }
        $packages = $packaging->getPackages();

        $i = 1;
        foreach ($packages as $packageId => $package) {
            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->drawRectangle(25, $this->y + 15, 190, $this->y - 35);
            $page->drawRectangle(190, $this->y + 15, 350, $this->y - 35);
            $page->drawRectangle(350, $this->y + 15, 570, $this->y - 35);

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->drawRectangle(520, $this->y + 15, 570, $this->y - 5);

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $packageText = Mage::helper('sales')->__('Package') . ' ' . $i;
            $page->drawText($packageText, 525, $this->y , 'UTF-8');
            $i ++;

            $package = new Varien_Object($package);
            $params = new Varien_Object($package->getParams());
            $dimensionUnits = Mage::helper('usa')->getMeasureDimensionName($params->getDimensionUnits());

            $typeText = Mage::helper('sales')->__('Type') . ' : '
                . $packaging->getContainerTypeByCode($params->getContainer());
            $page->drawText($typeText, 35, $this->y , 'UTF-8');

            if ($params->getLength() != null) {
                $lengthText = $params->getLength() .' '. $dimensionUnits;
            } else {
                $lengthText = '--';
            }
            $lengthText = Mage::helper('sales')->__('Length') . ' : ' . $lengthText;
            $page->drawText($lengthText, 200, $this->y , 'UTF-8');

            if ($params->getDeliveryConfirmation() != null) {
                $confirmationText = Mage::helper('sales')->__('Signature Confirmation')
                    . ' : '
                    . $packaging->getDeliveryConfirmationTypeByCode($params->getDeliveryConfirmation());
                $page->drawText($confirmationText, 355, $this->y , 'UTF-8');
            }

            $this->y = $this->y - 10;

            $weightText = Mage::helper('sales')->__('Total Weight') . ' : ' . $params->getWeight() .' '
                . Mage::helper('usa')->getMeasureWeightName($params->getWeightUnits());
            $page->drawText($weightText, 35, $this->y , 'UTF-8');

            if ($params->getWidth() != null) {
                $widthText = $params->getWidth() .' '. $dimensionUnits;
            } else {
                $widthText = '--';
            }
            $widthText = Mage::helper('sales')->__('Width') . ' : ' . $widthText;
            $page->drawText($widthText, 200, $this->y , 'UTF-8');

            $this->y = $this->y - 10;

            if ($params->getHeight() != null) {
                $heightText = $params->getHeight() .' '. $dimensionUnits;
            } else {
                $heightText = '--';
            }
            $heightText = Mage::helper('sales')->__('Height') . ' : ' . $heightText;
            $page->drawText($heightText, 200, $this->y , 'UTF-8');

            $this->y = $this->y - 10;
            $sizeText = $this->_getSizeText($params);
            if ($sizeText) {
                $page->drawText($sizeText, 35, $this->y , 'UTF-8');
            }
            if ($params->getGirth() != null) {
                $dimensionGirthUnits = Mage::helper('usa')->getMeasureDimensionName($params->getGirthDimensionUnits());
                $girthText = Mage::helper('sales')->__('Girth')
                             . ' : ' . $params->getGirth() . ' ' . $dimensionGirthUnits;
                $page->drawText($girthText, 200, $this->y , 'UTF-8');
            }

            $this->y = $this->y - 5;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->drawRectangle(25, $this->y, 570, $this->y - 30 - (count($package->getItems()) * 12));

            $this->y = $this->y - 10;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText(Mage::helper('sales')->__('Items in the Package'), 30, $this->y, 'UTF-8');

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->drawRectangle(30, $this->y - 5, 300, $this->y - 15);
            $page->drawRectangle(300, $this->y - 5, 450, $this->y - 15);
            $page->drawRectangle(450, $this->y - 5, 565, $this->y - 15);

            $this->y = $this->y - 12;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText(Mage::helper('sales')->__('Product'), 35, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Weight'), 305, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Qty'), 455, $this->y, 'UTF-8');

            foreach ($package->getItems() as $itemId => $item) {
                $item = new Varien_Object($item);

                $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
                $page->drawRectangle(30, $this->y - 3, 300, $this->y - 15);
                $page->drawRectangle(300, $this->y - 3, 450, $this->y - 15);
                $page->drawRectangle(450, $this->y - 3, 565, $this->y - 15);

                $this->y = $this->y - 12;
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                $page->drawText($item->getName(), 35, $this->y, 'UTF-8');
                $page->drawText($item->getWeight(), 305, $this->y, 'UTF-8');
                $page->drawText($item->getQty()*1, 455, $this->y, 'UTF-8');

            }
            $this->y = $this->y - 30;
        }
        return $this;
    }

    /**
     * Get package size from params either from system config
     *
     * @param Varien_Object $params
     * @return string
     */
    protected function _getSizeText($params)
    {
        $sizeText = '';
        $uspsModel = Mage::getSingleton('usa/shipping_carrier_usps');
        if ($params->getSize() != null) {
            $sizeText = Mage::helper('sales')->__('Size') . ' : ' . $uspsModel->getCode('size', $params->getSize());
        }
        return $sizeText;
    }

}
