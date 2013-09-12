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
 * Order RMA Grid
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Order_View_Tab_Rma
    extends Magento_Rma_Block_Adminhtml_Rma_Grid
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $storeManager, $urlModel, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('order_rma');
        $this->setUseAjax(true);
    }

    /**
     * Configuring and setting collection
     *
     * @return Magento_Rma_Block_Adminhtml_Order_View_Tab_Rma
     */
    protected function _beforePrepareCollection()
    {
        $orderId = null;

        if ($this->getOrder() && $this->getOrder()->getId()) {
            $orderId = $this->getOrder()->getId();
        } elseif ($this->getOrderId()) {
            $orderId = $this->getOrderId();
        }
        if ($orderId) {
            /** @var $collection Magento_Rma_Model_Resource_Rma_Grid_Collection */
            $collection = Mage::getResourceModel('Magento_Rma_Model_Resource_Rma_Grid_Collection')
                ->addFieldToFilter('order_id', $orderId);
            $this->setCollection($collection);
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        unset($this->_columns['order_increment_id']);
        unset($this->_columns['order_date']);
    }

    /**
     * Get Url to action
     *
     * @param  string $action action Url part
     * @return string
     */
    protected function _getControllerUrl($action = '')
    {
        return '*/rma/' . $action;
    }

    /**
     * Get Url to action to reload grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/rma/rmaOrder', array('_current' => true));
    }

    /**
     * Retrieve order model instance
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * ######################## TAB settings #################################
     */
    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Returns');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Returns');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
