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
 * Sales Order Creditmemo PDF model
 */
namespace Magento\Sales\Model\Order\Pdf;

class Creditmemo extends \Magento\Sales\Model\Order\Pdf\AbstractPdf
{
    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Helper\String $coreString
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Config $coreConfig
     * @param \Magento\Core\Model\Dir $coreDir
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Magento\Core\Model\Translate $translate
     * @param \Magento\Sales\Model\Order\Pdf\TotalFactory $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Helper\String $coreString,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Config $coreConfig,
        \Magento\Core\Model\Dir $coreDir,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Core\Model\Translate $translate,
        \Magento\Sales\Model\Order\Pdf\TotalFactory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
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
     * @param  \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 30);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => __('Products'),
            'feed' => 35,
        );

        $lines[0][] = array(
            'text'  => $this->_coreString->str_split(__('SKU'), 12, true, true),
            'feed'  => 255,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => $this->_coreString->str_split(__('Total (ex)'), 12, true, true),
            'feed'  => 330,
            'align' => 'right',
            //'width' => 50,
        );

        $lines[0][] = array(
            'text'  => $this->_coreString->str_split(__('Discount'), 12, true, true),
            'feed'  => 380,
            'align' => 'right',
            //'width' => 50,
        );

        $lines[0][] = array(
            'text'  => $this->_coreString->str_split(__('Qty'), 12, true, true),
            'feed'  => 445,
            'align' => 'right',
            //'width' => 30,
        );

        $lines[0][] = array(
            'text'  => $this->_coreString->str_split(__('Tax'), 12, true, true),
            'feed'  => 495,
            'align' => 'right',
            //'width' => 45,
        );

        $lines[0][] = array(
            'text'  => $this->_coreString->str_split(__('Total (inc)'), 12, true, true),
            'feed'  => 565,
            'align' => 'right'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 10
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Return PDF document
     *
     * @param  array $creditmemos
     * @return \Zend_Pdf
     */
    public function getPdf($creditmemos = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('creditmemo');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($creditmemos as $creditmemo) {
            if ($creditmemo->getStoreId()) {
                $this->_locale->emulate($creditmemo->getStoreId());
                $this->_storeManager->setCurrentStore($creditmemo->getStoreId());
            }
            $page  = $this->newPage();
            $order = $creditmemo->getOrder();
            /* Add image */
            $this->insertLogo($page, $creditmemo->getStore());
            /* Add address */
            $this->insertAddress($page, $creditmemo->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                $this->_coreStoreConfig->getConfigFlag(
                    self::XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID,
                    $order->getStoreId()
            ));
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                __('Credit Memo # ') . $creditmemo->getIncrementId()
            );
            /* Add table head */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($creditmemo->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $creditmemo);
        }
        $this->_afterGetPdf();
        if ($creditmemo->getStoreId()) {
            $this->_locale->revert();
        }
        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return \Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        $page = parent::newPage($settings);
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }
}
