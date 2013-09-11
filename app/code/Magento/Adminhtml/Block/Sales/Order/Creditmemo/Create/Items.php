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
 * Adminhtml creditmemo items grid
 */
class Magento_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items extends Magento_Adminhtml_Block_Sales_Items_Abstract
{
    protected $_canReturnToStock;

    /**
     * Sales data
     *
     * @var Magento_Sales_Helper_Data
     */
    protected $_salesData = null;

    /**
     * @param Magento_Sales_Helper_Data $salesData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Sales_Helper_Data $salesData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_salesData = $salesData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prepare child blocks
     *
     * @return Magento_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('creditmemo_item_container'),'".$this->getUpdateUrl()."')";
        $this->addChild('update_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Update Qty\'s'),
            'class'     => 'update-button',
            'onclick'   => $onclick,
        ));

        if ($this->getCreditmemo()->canRefund()) {
            if ($this->getCreditmemo()->getInvoice() && $this->getCreditmemo()->getInvoice()->getTransactionId()) {
                $this->addChild('submit_button', 'Magento_Adminhtml_Block_Widget_Button', array(
                    'label'     => __('Refund'),
                    'class'     => 'save submit-button refund',
                    'onclick'   => 'disableElements(\'submit-button\');submitCreditMemo()',
                ));
            }
            $this->addChild('submit_offline', 'Magento_Adminhtml_Block_Widget_Button', array(
                'label'     => __('Refund Offline'),
                'class'     => 'save submit-button',
                'onclick'   => 'disableElements(\'submit-button\');submitCreditMemoOffline()',
            ));

        } else {
            $this->addChild('submit_button', 'Magento_Adminhtml_Block_Widget_Button', array(
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
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getCreditmemo()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Magento_Sales_Model_Order_Creditmemo
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
     * @return Magento_Sales_Model_Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }

    public function canEditQty()
    {
        if ($this->getCreditmemo()->getOrder()->getPayment()->canRefund()) {
            return $this->getCreditmemo()->getOrder()->getPayment()->canRefundPartialPerInvoice();
        }
        return true;
    }

    public function getUpdateButtonHtml()
    {
        return $this->getChildHtml('update_button');
    }

    public function getUpdateUrl()
    {
        return $this->getUrl('*/*/updateQty', array(
            'order_id' => $this->getCreditmemo()->getOrderId(),
            'invoice_id' => $this->getRequest()->getParam('invoice_id', null),
        ));
    }

    public function canReturnToStock()
    {
        if (Mage::getStoreConfig(Magento_CatalogInventory_Model_Stock_Item::XML_PATH_CAN_SUBTRACT)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Whether to show 'Return to stock' column in creaditmemo grid
     * @return bool
     */
    public function canReturnItemsToStock()
    {
        if (is_null($this->_canReturnToStock)) {
            $this->_canReturnToStock = Mage::getStoreConfig(
                Magento_CatalogInventory_Model_Stock_Item::XML_PATH_CAN_SUBTRACT
            );
            if ($this->_canReturnToStock) {
                $canReturnToStock = false;
                foreach ($this->getCreditmemo()->getAllItems() as $item) {
                    $product = Mage::getModel('Magento_Catalog_Model_Product')
                        ->load($item->getOrderItem()->getProductId());
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

    public function canSendCreditmemoEmail()
    {
        return $this->_salesData->canSendNewCreditmemoEmail($this->getOrder()->getStore()->getId());
    }
}
