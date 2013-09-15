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
 * Sales order view items block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Order\Invoice;

class Items extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    public function getPrintInvoiceUrl($invoice)
    {
        return \Mage::getUrl('*/*/printInvoice', array('invoice_id' => $invoice->getId()));
    }

    public function getPrintAllInvoicesUrl($order)
    {
        return \Mage::getUrl('*/*/printInvoice', array('order_id' => $order->getId()));
    }

    /**
     * Get html of invoice totals block
     *
     * @param   \Magento\Sales\Model\Order\Invoice $invoice
     * @return  string
     */
    public function getInvoiceTotalsHtml($invoice)
    {
        $html = '';
        $totals = $this->getChildBlock('invoice_totals');
        if ($totals) {
            $totals->setInvoice($invoice);
            $html = $totals->toHtml();
        }
        return $html;
    }

    /**
     * Get html of invoice comments block
     *
     * @param   \Magento\Sales\Model\Order\Invoice $invoice
     * @return  string
     */
    public function getInvoiceCommentsHtml($invoice)
    {
        $html = '';
        $comments = $this->getChildBlock('invoice_comments');
        if ($comments) {
            $comments->setEntity($invoice)
                ->setTitle(__('About Your Invoice'));
            $html = $comments->toHtml();
        }
        return $html;
    }
}
