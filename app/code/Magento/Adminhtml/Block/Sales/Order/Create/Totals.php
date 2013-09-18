<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create totals block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Sales\Order\Create;

class Totals extends \Magento\Adminhtml\Block\Sales\Order\Create\AbstractCreate
{
    protected $_totalRenderers;
    protected $_defaultRenderer = 'Magento\Adminhtml\Block\Sales\Order\Create\Totals\DefaultTotals';

    /**
     * Sales data
     *
     * @var \Magento\Sales\Helper\Data
     */
    protected $_salesData = null;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @param \Magento\Sales\Helper\Data $salesData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        $this->_salesData = $salesData;
        $this->_coreConfig = $coreConfig;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_totals');
    }

    public function getTotals()
    {
        return $this->getQuote()->getTotals();
    }

    public function getHeaderText()
    {
        return __('Order Totals');
    }

    public function getHeaderCssClass()
    {
        return 'head-money';
    }

    protected function _getTotalRenderer($code)
    {
        $blockName = $code.'_total_renderer';
        $block = $this->getLayout()->getBlock($blockName);
        if (!$block) {
            $block = $this->_defaultRenderer;
            $config = $this->_coreConfig->getNode("global/sales/quote/totals/{$code}/admin_renderer");
            if ($config) {
                $block = (string) $config;
            }

            $block = $this->getLayout()->createBlock($block, $blockName);
        }
        /**
         * Transfer totals to renderer
         */
        $block->setTotals($this->getTotals());
        return $block;
    }

    public function renderTotal($total, $area = null, $colspan = 1)
    {
        return $this->_getTotalRenderer($total->getCode())
            ->setTotal($total)
            ->setColspan($colspan)
            ->setRenderingArea(is_null($area) ? -1 : $area)
            ->toHtml();
    }

    public function renderTotals($area = null, $colspan = 1)
    {
        $html = '';
        foreach ($this->getTotals() as $total) {
            if ($total->getArea() != $area && $area != -1) {
                continue;
            }
            $html .= $this->renderTotal($total, $area, $colspan);
        }
        return $html;
    }

    public function canSendNewOrderConfirmationEmail()
    {
        return $this->_salesData->canSendNewOrderConfirmationEmail($this->getQuote()->getStoreId());
    }
}
