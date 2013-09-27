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
class Magento_Sales_Model_Order_Pdf_Shipment extends Magento_Sales_Model_Order_Pdf_Abstract
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
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Core_Model_Dir $coreDir
     * @param Magento_Shipping_Model_Config $shippingConfig
     * @param Magento_Core_Model_Translate $translate
     * @param Magento_Sales_Model_Order_Pdf_TotalFactory $pdfTotalFactory
     * @param Magento_Sales_Model_Order_Pdf_ItemsFactory $pdfItemsFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Payment_Helper_Data $paymentData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_String $coreString,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Config $coreConfig,
        Magento_Core_Model_Dir $coreDir,
        Magento_Shipping_Model_Config $shippingConfig,
        Magento_Core_Model_Translate $translate,
        Magento_Sales_Model_Order_Pdf_TotalFactory $pdfTotalFactory,
        Magento_Sales_Model_Order_Pdf_ItemsFactory $pdfItemsFactory,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct(
            $paymentData,
            $coreData,
            $coreString,
            $coreStoreConfig,
            $coreConfig,
            $coreDir,
            $shippingConfig,
            $translate,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $data
        );
        $this->_locale = $locale;
        $this->_storeManager = $storeManager;
    }

    /**
     * Draw table header for product items
     *
     * @param  Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y-15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => __('Products'),
            'feed' => 100,
        );

        $lines[0][] = array(
            'text'  => __('Qty'),
            'feed'  => 35
        );

        $lines[0][] = array(
            'text'  => __('SKU'),
            'feed'  => 565,
            'align' => 'right'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 10
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Return PDF document
     *
     * @param  array $shipments
     * @return Zend_Pdf
     */
    public function getPdf($shipments = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                $this->_locale->emulate($shipment->getStoreId());
                $this->_storeManager->setCurrentStore($shipment->getStoreId());
            }
            $page  = $this->newPage();
            $order = $shipment->getOrder();
            /* Add image */
            $this->insertLogo($page, $shipment->getStore());
            /* Add address */
            $this->insertAddress($page, $shipment->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $shipment,
                $this->_coreStoreConfig->getConfigFlag(
                    self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID,
                    $order->getStoreId()
            ));
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Packing Slip # ') . $shipment->getIncrementId());
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($shipment->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
        }
        $this->_afterGetPdf();
        if ($shipment->getStoreId()) {
            $this->_locale->revert();
        }
        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }
}
