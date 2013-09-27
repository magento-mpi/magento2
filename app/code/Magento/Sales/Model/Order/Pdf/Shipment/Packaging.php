<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales Order Shipment PDF model
 */
class Magento_Sales_Model_Order_Pdf_Shipment_Packaging extends Magento_Sales_Model_Order_Pdf_Abstract
{
    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Usa data
     *
     * @var Magento_Usa_Helper_Data
     */
    protected $_usaData;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @param Magento_Usa_Helper_Data $usaData
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_Core_Model_Store_ConfigInterface $coreStoreConfig
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Core_Model_Dir $coreDir
     * @param Magento_Shipping_Model_Config $shippingConfig
     * @param Magento_Core_Model_Translate $translate
     * @param Magento_Sales_Model_Order_Pdf_TotalFactory $pdfTotalFactory
     * @param Magento_Sales_Model_Order_Pdf_ItemsFactory $pdfItemsFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Layout $layout
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Usa_Helper_Data $usaData,
        Magento_Payment_Helper_Data $paymentData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_String $coreString,
        Magento_Core_Model_Store_ConfigInterface $coreStoreConfig,
        Magento_Core_Model_Config $coreConfig,
        Magento_Core_Model_Dir $coreDir,
        Magento_Shipping_Model_Config $shippingConfig,
        Magento_Core_Model_Translate $translate,
        Magento_Sales_Model_Order_Pdf_TotalFactory $pdfTotalFactory,
        Magento_Sales_Model_Order_Pdf_ItemsFactory $pdfItemsFactory,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Layout $layout,
        array $data = array()
    ) {
        $this->_usaData = $usaData;
        $this->_locale = $locale;
        $this->_storeManager = $storeManager;
        $this->_layout = $layout;
        parent::__construct($paymentData, $coreData, $coreString, $coreStoreConfig, $coreConfig, $coreDir,
            $shippingConfig, $translate, $pdfTotalFactory, $pdfItemsFactory, $data);
    }

    /**
     * Format pdf file
     *
     * @param  null $shipment
     * @return Zend_Pdf
     */
    public function getPdf($shipment = null)
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $page = $this->newPage();

        if ($shipment->getStoreId()) {
            $this->_locale->emulate($shipment->getStoreId());
            $this->_storeManager->setCurrentStore($shipment->getStoreId());
        }

        $this->_setFontRegular($page);
        $this->_drawHeaderBlock($page);

        $this->y = 740;
        $this->_drawPackageBlock($page);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_afterGetPdf();

        if ($shipment->getStoreId()) {
            $this->_locale->revert();
        }
        return $pdf;
    }

    /**
     * Draw header block
     *
     * @param  Zend_Pdf_Page $page
     * @return Magento_Sales_Model_Order_Pdf_Shipment_Packaging
     */
    protected function _drawHeaderBlock(Zend_Pdf_Page $page) {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, 790, 570, 755);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawText(__('Packages'), 35, 770, 'UTF-8');
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));

        return $this;
    }

    /**
     * Draw packages block
     *
     * @param  Zend_Pdf_Page $page
     * @return Magento_Sales_Model_Order_Pdf_Shipment_Packaging
     */
    protected function _drawPackageBlock(Zend_Pdf_Page $page)
    {
        if ($this->getPackageShippingBlock()) {
            $packaging = $this->getPackageShippingBlock();
        } else {
            $packaging = $this->_layout->getBlockSingleton('Magento_Adminhtml_Block_Sales_Order_Shipment_Packaging');
        }
        $packages = $packaging->getPackages();

        $packageNum = 1;
        foreach ($packages as $package) {
            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->drawRectangle(25, $this->y + 15, 190, $this->y - 35);
            $page->drawRectangle(190, $this->y + 15, 350, $this->y - 35);
            $page->drawRectangle(350, $this->y + 15, 570, $this->y - 35);

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->drawRectangle(520, $this->y + 15, 570, $this->y - 5);

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $packageText = __('Package') . ' ' . $packageNum;
            $page->drawText($packageText, 525, $this->y , 'UTF-8');
            $packageNum++;

            $package = new Magento_Object($package);
            $params = new Magento_Object($package->getParams());
            $dimensionUnits = $this->_usaData->getMeasureDimensionName($params->getDimensionUnits());

            $typeText = __('Type') . ' : '
                . $packaging->getContainerTypeByCode($params->getContainer());
            $page->drawText($typeText, 35, $this->y , 'UTF-8');

            if ($params->getLength() != null) {
                $lengthText = $params->getLength() .' '. $dimensionUnits;
            } else {
                $lengthText = '--';
            }
            $lengthText = __('Length') . ' : ' . $lengthText;
            $page->drawText($lengthText, 200, $this->y , 'UTF-8');

            if ($params->getDeliveryConfirmation() != null) {
                $confirmationText = __('Signature Confirmation')
                    . ' : '
                    . $packaging->getDeliveryConfirmationTypeByCode($params->getDeliveryConfirmation());
                $page->drawText($confirmationText, 355, $this->y , 'UTF-8');
            }

            $this->y = $this->y - 10;

            if ($packaging->displayCustomsValue() != null) {
                $customsValueText = __('Customs Value')
                    . ' : '
                    . $packaging->displayPrice($params->getCustomsValue());
                $page->drawText($customsValueText, 35, $this->y , 'UTF-8');
            }
            if ($params->getWidth() != null) {
                $widthText = $params->getWidth() .' '. $dimensionUnits;
            } else {
                $widthText = '--';
            }
            $widthText = __('Width') . ' : ' . $widthText;
            $page->drawText($widthText, 200, $this->y , 'UTF-8');

            if ($params->getContentType() != null) {
                if ($params->getContentType() == 'OTHER') {
                    $contentsValue = $params->getContentTypeOther();
                } else {
                    $contentsValue = $packaging->getContentTypeByCode($params->getContentType());
                }
                $contentsText = __('Contents') . ' : ' . $contentsValue;
                $page->drawText($contentsText, 355, $this->y , 'UTF-8');
            }

            $this->y = $this->y - 10;

            $weightText = __('Total Weight') . ' : ' . $params->getWeight() .' '
                . $this->_usaData->getMeasureWeightName($params->getWeightUnits());
            $page->drawText($weightText, 35, $this->y , 'UTF-8');

            if ($params->getHeight() != null) {
                $heightText = $params->getHeight() .' '. $dimensionUnits;
            } else {
                $heightText = '--';
            }
            $heightText = __('Height') . ' : ' . $heightText;
            $page->drawText($heightText, 200, $this->y , 'UTF-8');

            $this->y = $this->y - 10;

            if ($params->getSize()) {
                $sizeText = __('Size') . ' : ' . ucfirst(strtolower($params->getSize()));
                $page->drawText($sizeText, 35, $this->y , 'UTF-8');
            }
            if ($params->getGirth() != null) {
                $dimensionGirthUnits = $this->_usaData->getMeasureDimensionName($params->getGirthDimensionUnits());
                $girthText = __('Girth')
                             . ' : ' . $params->getGirth() . ' ' . $dimensionGirthUnits;
                $page->drawText($girthText, 200, $this->y , 'UTF-8');
            }

            $this->y = $this->y - 5;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->drawRectangle(25, $this->y, 570, $this->y - 30 - (count($package->getItems()) * 12));

            $this->y = $this->y - 10;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText(__('Items in the Package'), 30, $this->y, 'UTF-8');

            $txtIndent = 5;
            $itemCollsNumber = $packaging->displayCustomsValue() ? 5 : 4;
            $itemCollsX[0] = 30; //  coordinate for Product name
            $itemCollsX[1] = 250; // coordinate for Product name
            $itemCollsXEnd = 565;
            $itemCollsXStep = round(($itemCollsXEnd - $itemCollsX[1]) / ($itemCollsNumber - 1));
            // calculate coordinates for all other cells (Weight, Customs Value, Qty Ordered, Qty)
            for ($i = 2; $i <= $itemCollsNumber; $i++) {
                $itemCollsX[$i] = $itemCollsX[$i-1] + $itemCollsXStep;
            }

            $i = 0;
            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->drawRectangle($itemCollsX[$i], $this->y - 5, $itemCollsX[++$i], $this->y - 15);
            $page->drawRectangle($itemCollsX[$i], $this->y - 5, $itemCollsX[++$i], $this->y - 15);
            $page->drawRectangle($itemCollsX[$i], $this->y - 5, $itemCollsX[++$i], $this->y - 15);
            $page->drawRectangle($itemCollsX[$i], $this->y - 5, $itemCollsX[++$i], $this->y - 15);
            $page->drawRectangle($itemCollsX[$i], $this->y - 5, $itemCollsXEnd, $this->y - 15);

            $this->y = $this->y - 12;
            $i = 0;

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText(__('Product'), $itemCollsX[$i] + $txtIndent, $this->y, 'UTF-8');
            $page->drawText(__('Weight'), $itemCollsX[++$i] + $txtIndent, $this->y, 'UTF-8');
            if ($packaging->displayCustomsValue()) {
                $page->drawText(
                    __('Customs Value'),
                    $itemCollsX[++$i] + $txtIndent,
                    $this->y,
                    'UTF-8'
                );
            }
            $page->drawText(
                __('Qty Ordered'), $itemCollsX[++$i] + $txtIndent, $this->y, 'UTF-8'
            );
            $page->drawText(__('Qty'), $itemCollsX[++$i] + $txtIndent, $this->y, 'UTF-8');

            $i = 0;
            foreach ($package->getItems() as $itemId => $item) {
                $item = new Magento_Object($item);
                $i = 0;

                $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
                $page->drawRectangle($itemCollsX[$i], $this->y - 3, $itemCollsX[++$i], $this->y - 15);
                $page->drawRectangle($itemCollsX[$i], $this->y - 3, $itemCollsX[++$i], $this->y - 15);
                $page->drawRectangle($itemCollsX[$i], $this->y - 3, $itemCollsX[++$i], $this->y - 15);
                $page->drawRectangle($itemCollsX[$i], $this->y - 3, $itemCollsX[++$i], $this->y - 15);
                $page->drawRectangle($itemCollsX[$i], $this->y - 3, $itemCollsXEnd, $this->y - 15);

                $this->y = $this->y - 12;
                $i = 0;
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                $page->drawText($item->getName(), $itemCollsX[$i] + $txtIndent, $this->y, 'UTF-8');
                $page->drawText($item->getWeight(), $itemCollsX[++$i] + $txtIndent, $this->y, 'UTF-8');
                if ($packaging->displayCustomsValue()) {
                    $page->drawText(
                        $packaging->displayPrice($item->getCustomsValue()),
                        $itemCollsX[++$i] + $txtIndent,
                        $this->y,
                        'UTF-8'
                    );
                }
                $page->drawText(
                    $packaging->getQtyOrderedItem($item->getOrderItemId()),
                    $itemCollsX[++$i] + $txtIndent,
                    $this->y,
                    'UTF-8'
                );
                $page->drawText($item->getQty()*1, $itemCollsX[++$i] + $txtIndent, $this->y, 'UTF-8');
            }
            $this->y = $this->y - 30;
        }
        return $this;
    }
}
