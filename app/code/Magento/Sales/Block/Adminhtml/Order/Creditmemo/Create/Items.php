<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create;

/**
 * Adminhtml creditmemo items grid
 */
class Items extends \Magento\Sales\Block\Adminhtml\Items\AbstractItems
{
    /**
     * @var bool
     */
    protected $_canReturnToStock;

    /**
     * Sales data
     *
     * @var \Magento\Sales\Helper\Data
     */
    protected $_salesData = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Registry $registry
     * @param \Magento\Sales\Helper\Data $salesData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Registry $registry,
        \Magento\Sales\Helper\Data $salesData,
        array $data = array()
    ) {
        $this->_salesData = $salesData;
        parent::__construct($context, $productFactory, $registry, $data);
    }

    /**
     * Prepare child blocks
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('creditmemo_item_container'),'".$this->getUpdateUrl()."')";
        $this->addChild('update_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Update Qty\'s'),
            'class'     => 'update-button',
            'onclick'   => $onclick,
        ));

        if ($this->getCreditmemo()->canRefund()) {
            if ($this->getCreditmemo()->getInvoice() && $this->getCreditmemo()->getInvoice()->getTransactionId()) {
                $this->addChild('submit_button', 'Magento\Backend\Block\Widget\Button', array(
                    'label'     => __('Refund'),
                    'class'     => 'save submit-button refund',
                    'onclick'   => 'disableElements(\'submit-button\');submitCreditMemo()',
                ));
            }
            $this->addChild('submit_offline', 'Magento\Backend\Block\Widget\Button', array(
                'label'     => __('Refund Offline'),
                'class'     => 'save submit-button',
                'onclick'   => 'disableElements(\'submit-button\');submitCreditMemoOffline()',
            ));

        } else {
            $this->addChild('submit_button', 'Magento\Backend\Block\Widget\Button', array(
                'label'     => __('Refund Offline'),
                'class'     => 'save submit-button',
                'onclick'   => 'disableElements(\'submit-button\');submitCreditMemoOffline()',
            ));
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve invoice order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getCreditmemo()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getSource()
    {
        return $this->getCreditmemo();
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    public function getOrderTotalData()
    {
        return array();
    }

    /**
     * Retrieve order totalbar block data
     *
     * @return array
     */
    public function getOrderTotalbarData()
    {
        $this->setPriceDataObject($this->getOrder());

        $totalbarData = array();
        $totalbarData[] = array(__('Paid Amount'), $this->displayPriceAttribute('total_invoiced'), false);
        $totalbarData[] = array(__('Refund Amount'), $this->displayPriceAttribute('total_refunded'), false);
        $totalbarData[] = array(__('Shipping Amount'), $this->displayPriceAttribute('shipping_invoiced'), false);
        $totalbarData[] = array(__('Shipping Refund'), $this->displayPriceAttribute('shipping_refunded'), false);
        $totalbarData[] = array(__('Order Grand Total'), $this->displayPriceAttribute('grand_total'), true);
        return $totalbarData;
    }

    /**
     * Retrieve creditmemo model instance
     *
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }

    /**
     * Check if allow to edit qty
     *
     * @return bool|true
     */
    public function canEditQty()
    {
        if ($this->getCreditmemo()->getOrder()->getPayment()->canRefund()) {
            return $this->getCreditmemo()->getOrder()->getPayment()->canRefundPartialPerInvoice();
        }
        return true;
    }

    /**
     * Get update button html
     *
     * @return string
     */
    public function getUpdateButtonHtml()
    {
        return $this->getChildHtml('update_button');
    }

    /**
     * Get update url
     *
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('sales/*/updateQty', array(
            'order_id' => $this->getCreditmemo()->getOrderId(),
            'invoice_id' => $this->getRequest()->getParam('invoice_id', null),
        ));
    }

    /**
     * Check if allow to return stock
     *
     * @return bool
     */
    public function canReturnToStock()
    {
        $canReturnToStock = $this->_storeConfig->getConfig(
            \Magento\CatalogInventory\Model\Stock\Item::XML_PATH_CAN_SUBTRACT
        );
        if ($canReturnToStock) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Whether to show 'Return to stock' column in creaditmemo grid
     *
     * @return bool
     */
    public function canReturnItemsToStock()
    {
        if (is_null($this->_canReturnToStock)) {
            $this->_canReturnToStock = $this->_storeConfig->getConfig(
                \Magento\CatalogInventory\Model\Stock\Item::XML_PATH_CAN_SUBTRACT
            );
            if ($this->_canReturnToStock) {
                $canReturnToStock = false;
                foreach ($this->getCreditmemo()->getAllItems() as $item) {
                    $product = $this->_productFactory->create()->load($item->getOrderItem()->getProductId());
                    if ( $product->getId() && $product->getStockItem()->getManageStock() ) {
                        $item->setCanReturnToStock($canReturnToStock = true);
                    } else {
                        $item->setCanReturnToStock(false);
                    }
                }
                $this->getCreditmemo()->getOrder()->setCanReturnToStock($this->_canReturnToStock = $canReturnToStock);
            }
        }
        return $this->_canReturnToStock;
    }

    /**
     * Check allow to send new creditmemo email
     *
     * @return bool
     */
    public function canSendCreditmemoEmail()
    {
        return $this->_salesData->canSendNewCreditmemoEmail($this->getOrder()->getStore()->getId());
    }
}
