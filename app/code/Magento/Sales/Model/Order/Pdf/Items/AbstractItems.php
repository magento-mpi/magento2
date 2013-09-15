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
 * Sales Order Pdf Items renderer Abstract
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Order\Pdf\Items;

abstract class AbstractItems extends \Magento\Core\Model\AbstractModel
{
    /**
     * Order model
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * Source model (invoice, shipment, creditmemo)
     *
     * @var \Magento\Core\Model\AbstractModel
     */
    protected $_source;

    /**
     * Item object
     *
     * @var \Magento\Object
     */
    protected $_item;

    /**
     * Pdf object
     *
     * @var \Magento\Sales\Model\Order\Pdf\AbstractPdf
     */
    protected $_pdf;

    /**
     * Pdf current page
     *
     * @var \Zend_Pdf_Page
     */
    protected $_pdfPage;

    /**
     * Tax data
     *
     * @var Magento_Tax_Helper_Data
     */
    protected $_taxData = null;

    /**
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Tax_Helper_Data $taxData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_taxData = $taxData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Set order model
     *
     * @param  \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
     */
    public function setOrder(\Magento\Sales\Model\Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Set Source model
     *
     * @param  \Magento\Core\Model\AbstractModel $source
     * @return \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
     */
    public function setSource(\Magento\Core\Model\AbstractModel $source)
    {
        $this->_source = $source;
        return $this;
    }

    /**
     * Set item object
     *
     * @param  \Magento\Object $item
     * @return \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
     */
    public function setItem(\Magento\Object $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Set Pdf model
     *
     * @param  \Magento\Sales\Model\Order\Pdf\AbstractPdf $pdf
     * @return \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
     */
    public function setPdf(\Magento\Sales\Model\Order\Pdf\AbstractPdf $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Set current page
     *
     * @param  \Zend_Pdf_Page $page
     * @return \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
     */
    public function setPage(\Zend_Pdf_Page $page)
    {
        $this->_pdfPage = $page;
        return $this;
    }

    /**
     * Retrieve order object
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        if (is_null($this->_order)) {
            \Mage::throwException(__('The order object is not specified.'));
        }
        return $this->_order;
    }

    /**
     * Retrieve source object
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Core\Model\AbstractModel
     */
    public function getSource()
    {
        if (is_null($this->_source)) {
            \Mage::throwException(__('The source object is not specified.'));
        }
        return $this->_source;
    }

    /**
     * Retrieve item object
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Object
     */
    public function getItem()
    {
        if (is_null($this->_item)) {
            \Mage::throwException(__('An item object is not specified.'));
        }
        return $this->_item;
    }

    /**
     * Retrieve Pdf model
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Sales\Model\Order\Pdf\AbstractPdf
     */
    public function getPdf()
    {
        if (is_null($this->_pdf)) {
            \Mage::throwException(__('A PDF object is not specified.'));
        }
        return $this->_pdf;
    }

    /**
     * Retrieve Pdf page object
     *
     * @throws \Magento\Core\Exception
     * @return \Zend_Pdf_Page
     */
    public function getPage()
    {
        if (is_null($this->_pdfPage)) {
            \Mage::throwException(__('A PDF page object is not specified.'));
        }
        return $this->_pdfPage;
    }

    /**
     * Draw item line
     *
     */
    abstract public function draw();

    /**
     * Format option value process
     *
     * @param  $value
     * @return string
     */
    protected function _formatOptionValue($value)
    {
        $order = $this->getOrder();

        $resultValue = '';
        if (is_array($value)) {
            if (isset($value['qty'])) {
                $resultValue .= sprintf('%d', $value['qty']) . ' x ';
            }

            $resultValue .= $value['title'];

            if (isset($value['price'])) {
                $resultValue .= " " . $order->formatPrice($value['price']);
            }
            return  $resultValue;
        } else {
            return $value;
        }
    }

    /**
     * Get array of arrays with item prices information for display in PDF
     * array(
     *  $index => array(
     *      'label'    => $label,
     *      'price'    => $price,
     *      'subtotal' => $subtotal
     *  )
     * )
     * @return array
     */
    public function getItemPricesForDisplay()
    {
        $order = $this->getOrder();
        $item  = $this->getItem();
        if ($this->_taxData->displaySalesBothPrices()) {
            $prices = array(
                array(
                    'label'    => __('Excl. Tax') . ':',
                    'price'    => $order->formatPriceTxt($item->getPrice()),
                    'subtotal' => $order->formatPriceTxt($item->getRowTotal())
                ),
                array(
                    'label'    => __('Incl. Tax') . ':',
                    'price'    => $order->formatPriceTxt($item->getPriceInclTax()),
                    'subtotal' => $order->formatPriceTxt($item->getRowTotalInclTax())
                ),
            );
        } elseif ($this->_taxData->displaySalesPriceInclTax()) {
            $prices = array(array(
                'price' => $order->formatPriceTxt($item->getPriceInclTax()),
                'subtotal' => $order->formatPriceTxt($item->getRowTotalInclTax()),
            ));
        } else {
            $prices = array(array(
                'price' => $order->formatPriceTxt($item->getPrice()),
                'subtotal' => $order->formatPriceTxt($item->getRowTotal()),
            ));
        }
        return $prices;
    }

    /**
     * Retrieve item options
     *
     * @return array
     */
    public function getItemOptions() {
        $result = array();
        if ($options = $this->getItem()->getOrderItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }

    /**
     * Set font as regular
     *
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($size = 7)
    {
        $font = \Zend_Pdf_Font::fontWithPath(\Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Re-4.4.1.ttf');
        $this->getPage()->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($size = 7)
    {
        $font = \Zend_Pdf_Font::fontWithPath(\Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf');
        $this->getPage()->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italic
     *
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($size = 7)
    {
        $font = \Zend_Pdf_Font::fontWithPath(\Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_It-2.8.2.ttf');
        $this->getPage()->setFont($font, $size);
        return $font;
    }

    /**
     * Return item Sku
     *
     * @param  $item
     * @return mixed
     */
    public function getSku($item)
    {
        if ($item->getOrderItem()->getProductOptionByCode('simple_sku'))
            return $item->getOrderItem()->getProductOptionByCode('simple_sku');
        else
            return $item->getSku();
    }
}
