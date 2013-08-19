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
 * Adminhtml transactions grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Transactions_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('order_transactions');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection for grid
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->getCollection();
        if (!$collection) {
            $collection = Mage::getResourceModel('Magento_Sales_Model_Resource_Order_Payment_Transaction_Collection');
        }
        $order = Mage::registry('current_order');
        if ($order) {
            $collection->addOrderIdFilter($order->getId());
        }
        $collection->addOrderInformation(array('increment_id'));
        $collection->addPaymentInformation(array('method'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', array(
            'header' => __('ID'),
            'index' => 'transaction_id',
            'type' => 'number',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ));

        $this->addColumn('increment_id', array(
            'header' => __('Order ID'),
            'index' => 'increment_id',
            'type' => 'text',
            'header_css_class' => 'col-order-id',
            'column_css_class' => 'col-order-id'
        ));

        $this->addColumn('txn_id', array(
            'header' => __('Transaction ID'),
            'index' => 'txn_id',
            'type' => 'text',
            'header_css_class' => 'col-transaction-id',
            'column_css_class' => 'col-transaction-id'
        ));

        $this->addColumn('parent_txn_id', array(
            'header' => __('Parent Transaction ID'),
            'index' => 'parent_txn_id',
            'type' => 'text',
            'header_css_class' => 'col-parent-transaction-id',
            'column_css_class' => 'col-parent-transaction-id'
        ));

        $this->addColumn('method', array(
            'header' => __('Payment Method'),
            'index' => 'method',
            'type' => 'options',
            'options' => Mage::helper('Magento_Payment_Helper_Data')->getPaymentMethodList(true),
            'option_groups' => Mage::helper('Magento_Payment_Helper_Data')->getPaymentMethodList(true, true, true),
            'header_css_class' => 'col-method',
            'column_css_class' => 'col-method'
        ));

        $this->addColumn('txn_type', array(
            'header' => __('Transaction Type'),
            'index' => 'txn_type',
            'type' => 'options',
            'options' => Mage::getSingleton('Magento_Sales_Model_Order_Payment_Transaction')->getTransactionTypes(),
            'header_css_class' => 'col-transaction-type',
            'column_css_class' => 'col-transaction-type'
        ));

        $this->addColumn('is_closed', array(
            'header' => __('Closed'),
            'index' => 'is_closed',
            'width' => 1,
            'type' => 'options',
            'align' => 'center',
            'options' => array(
                1 => __('Yes'),
                0 => __('No'),
            ),
            'header_css_class' => 'col-closed',
            'column_css_class' => 'col-closed'
        ));

        $this->addColumn('created_at', array(
            'header' => __('Created'),
            'index' => 'created_at',
            'width' => 1,
            'type' => 'datetime',
            'align' => 'center',
            'default' => __('N/A'),
            'html_decorators' => array('nobr'),
            'header_css_class' => 'col-period',
            'column_css_class' => 'col-period'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Retrieve row url
     *
     * @return string
     */
    public function getRowUrl($item)
    {
        return $this->getUrl('*/*/view', array('txn_id' => $item->getId()));
    }
}
