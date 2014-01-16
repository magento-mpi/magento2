<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rma PDF model
 */
namespace Magento\Rma\Model\Pdf;

class Rma extends \Magento\Sales\Model\Order\Pdf\AbstractPdf
{
    /**
     * Variable to store store-depended string values of attributes
     *
     * @var null|array
     */
    protected $_attributeOptionValues = null;

    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData;

    /**
     * Rma eav
     *
     * @var \Magento\Rma\Helper\Eav
     */
    protected $_rmaEav;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Stdlib\String $string
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     * @param \Magento\Core\Model\Translate $translate
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Magento\Sales\Model\Order\Pdf\Config $pdfConfig
     * @param \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Rma\Helper\Eav $rmaEav
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Stdlib\String $string,
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig,
        \Magento\Core\Model\Translate $translate,
        \Magento\Filesystem $filesystem,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Rma\Helper\Eav $rmaEav,
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_rmaEav = $rmaEav;
        $this->_rmaData = $rmaData;
        $this->_storeManager = $storeManager;

        parent::__construct(
            $paymentData,
            $string,
            $coreStoreConfig,
            $translate,
            $filesystem,
            $shippingConfig,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $locale,
            $data
        );
    }

    /**
     * Retrieve PDF
     *
     * @param array $rmaArray
     * @throws \Magento\Core\Exception
     * @return \Zend_Pdf
     */
    public function getPdf($rmaArray = array())
    {
        $this->_beforeGetPdf();

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        if (!(is_array($rmaArray) && (count($rmaArray) == 1))) {
            throw new \Magento\Core\Exception(__('Only one RMA is available for printing'));
        }
        $rma = $rmaArray[0];

        $storeId = $rma->getOrder()->getStore()->getId();
        if ($storeId) {
            $this->locale->emulate($storeId);
            $this->_storeManager->setCurrentStore($storeId);
        }

        $page = $this->newPage();

        /* Add image */
        $this->insertLogo($page, $storeId);

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 5);

        $page->setLineWidth(0.5);
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->drawLine(125, 825, 125, 790);

        $page->setLineWidth(0);
        /* start y-position for next block */
        $this->y = 820;

        /* Add head */
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->drawRectangle(25, $this->y - 30, 570, $this->y - 75);

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $this->_setFontRegular($page);

        $page->drawText(
            __('Return # ') . $rma->getIncrementId() . ' - ' . $rma->getStatusLabel(),
            35,
            $this->y - 40,
            'UTF-8'
        );

        $page->drawText(
            __('Return Date: ') .
                $this->locale->formatDate($rma->getDateRequested(), 'medium', false),
            35,
            $this->y - 50,
            'UTF-8'
        );

        $page->drawText(
            __('Order # ') . $rma->getOrder()->getIncrementId(),
            35,
            $this->y - 60,
            'UTF-8'
        );

        $text = __('Order Date: ');
        $text .= $this->locale->formatDate(
            $rma->getOrder()->getCreatedAtStoreDate(),
            'medium',
            false
        );
        $page->drawText(
            $text,
            35,
            $this->y - 70,
            'UTF-8'
        );

        /* start y-position for next block */
        $this->y = $this->y - 80;

        /* add address blocks */
        $shippingAddress = $this->_formatAddress($rma->getOrder()->getShippingAddress()->format('pdf'));
        $returnAddress = $this
            ->_formatAddress($this->_rmaData
            ->getReturnAddress('pdf', array(), $this->getStoreId()));

        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 290, $this->y - 15);
        $page->drawRectangle(305, $this->y, 570, $this->y - 15);

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page);
        $page->drawText(__('Shipping Address:'), 35, $this->y - 10, 'UTF-8');

        $page->drawText(__('Return Address:'), 315, $this->y - 10, 'UTF-8');

        $y = $this->y - 15 - (max(count($shippingAddress), count($returnAddress)) * 10 + 5);

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, $this->y - 15, 290, $y);
        $page->drawRectangle(305, $this->y - 15, 570, $y);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page);

        $yStartAddresses = $this->y - 25;
        foreach ($shippingAddress as $value) {
            if ($value!=='') {
                $page->drawText(strip_tags(ltrim($value)), 35, $yStartAddresses, 'UTF-8');
                $yStartAddresses -=10;
            }
        }
        $yStartAddresses = $this->y - 25;
        foreach ($returnAddress as $value) {
            if ($value!=='') {
                $page->drawText(strip_tags(ltrim($value)), 315, $yStartAddresses, 'UTF-8');
                $yStartAddresses -=10;
            }

        }

        /* start y-position for next block */
        $this->y = $this->y - 20 - (max(count($shippingAddress), count($returnAddress)) * 10 + 5);

        /* Add table */
        $this->_setColumnXs();
        $this->_addItemTableHead($page);

        /* Add body */

        /** @var $collection \Magento\Rma\Model\Resource\Item\Collection */
        $collection = $rma->getItemsForDisplay();

        foreach ($collection as $item){

            if ($this->y < 15) {
                $page = $this->_addNewPage();
            }

            /* Draw item */
            $this->_drawRmaItem($item, $page);
        }

        if ($storeId) {
            $this->locale->revert();
        }

        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * Create new page, assign to PDF object and repeat table head there
     *
     * @return \Zend_Pdf_Page
     */
    protected function _addNewPage()
    {
        $page = $this->_getPdf()->newPage(\Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;

        $this->_addItemTableHead($page);
        return $page;
    }

    /**
     * Add items table head
     *
     * @param \Zend_Pdf_Page $page
     */
    protected function _addItemTableHead($page)
    {
        $this->_setFontRegular($page);
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y-15);
        $this->y -=10;

        $page->setFillColor(new \Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
        $page->drawText(
            __('Product Name'),
            $this->getProductNameX(),
            $this->y,
            'UTF-8'
        );
        $page->drawText(
            __('SKU'),
            $this->getProductSkuX(),
            $this->y,
            'UTF-8'
        );
        $page->drawText(
            __('Condition'),
            $this->getConditionX(),
            $this->y,
            'UTF-8'
        );
        $page->drawText(
            __('Resolution'),
            $this->getResolutionX(),
            $this->y,
            'UTF-8'
        );
        $page->drawText(
            __('Requested Qty'),
            $this->getQtyRequestedX(),
            $this->y,
            'UTF-8'
        );
        $page->drawText(
            __('Qty'),
            $this->getQtyX(),
            $this->y,
            'UTF-8'
        );
        $page->drawText(
            __('Status'),
            $this->getStatusX(),
            $this->y,
            'UTF-8'
        );

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

        $this->y -=15;
    }

    /**
     * Draw one line - rma item
     *
     * @param \Magento\Rma\Model\Item $item
     * @param \Zend_Pdf_Page $page
     */
    protected function _drawRmaItem($item, $page)
    {
        $productName = $this->string->split($item->getProductName(), 60, true, true);
        $productName = isset($productName[0]) ? $productName[0] : '';

        $page->drawText($productName, $this->getProductNameX(), $this->y, 'UTF-8');

        $productSku = $this->string->split($item->getProductSku(), 25);
        $productSku = isset($productSku[0]) ? $productSku[0] : '';
        $page->drawText($productSku, $this->getProductSkuX(), $this->y, 'UTF-8');

        $condition = $this->string->split(
            $this->_getOptionAttributeStringValue($item->getCondition()),
            25
        );
        $page->drawText($condition[0], $this->getConditionX(), $this->y, 'UTF-8');

        $resolution = $this->string->split(
            $this->_getOptionAttributeStringValue($item->getResolution()),
            25
        );
        $page->drawText($resolution[0], $this->getResolutionX(), $this->y, 'UTF-8');
        $page->drawText(
            $this->_rmaData->parseQuantity($item->getQtyRequested(), $item),
            $this->getQtyRequestedX(),
            $this->y,
            'UTF-8'
        );

        $page->drawText(
            $this->_rmaData->getQty($item),
            $this->getQtyX(),
            $this->y,
            'UTF-8'
        );

        $status = $this->string->split($item->getStatusLabel(), 25);
        $page->drawText($status[0], $this->getStatusX(), $this->y, 'UTF-8');

        $productOptions = $item->getOptions();
        if (is_array($productOptions) && !empty($productOptions)) {
            $this->_drawCustomOptions($productOptions, $page);
        }

        $this->y -= 10;
    }

    /**
     * Draw additional lines for item's custom options
     *
     * @param array $optionsArray
     * @param \Zend_Pdf_Page $page
     */
    protected function _drawCustomOptions($optionsArray, $page)
    {
        $this->_setFontRegular($page, 6);
        foreach ($optionsArray as $value) {
            $this->y -= 8;
            $optionRowString = $value['label'] . ': ' .
                (isset($value['print_value']) ? $value['print_value'] : $value['value']);
            $productOptions = $this->string->split($optionRowString, 60, true, true);
            $productOptions = isset($productOptions[0]) ? $productOptions[0] : '';
            $page->drawText($productOptions, $this->getProductNameX(), $this->y, 'UTF-8');
        }
        $this->_setFontRegular($page);
    }

    /**
     * Get string label of option-type item attributes
     *
     * @param int $attributeValue
     * @return string
     */
    protected function _getOptionAttributeStringValue($attributeValue)
    {
        if (is_null($this->_attributeOptionValues)) {
            $this->_attributeOptionValues = $this->_rmaEav->getAttributeOptionStringValues();
        }
        if (isset($this->_attributeOptionValues[$attributeValue])) {
            return $this->_attributeOptionValues[$attributeValue];
        } else {
            return '';
        }
    }

    /**
     * Sets X coordinates for columns
     *
     */
    protected function _setColumnXs()
    {
        $this->setProductNameX(35);
        $this->setProductSkuX(200);
        $this->setConditionX(280);
        $this->setResolutionX(360);
        $this->setQtyRequestedX(425);
        $this->setQtyX(490);
        $this->setStatusX(520);
    }
}
