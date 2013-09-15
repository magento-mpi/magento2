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
namespace Magento\Adminhtml\Block\Sales\Transactions;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Payment data
     *
     * @var Magento_Payment_Helper_Data
     */
    protected $_paymentData = null;

    /**
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Payment_Helper_Data $paymentData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_paymentData = $paymentData;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

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
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->getCollection();
        if (!$collection) {
            $collection = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Payment\Transaction\Collection');
        }
        $order = $this->_coreRegistry->registry('current_order');
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
     * @return \Magento\Adminhtml\Block\Widget\Grid
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
            'options' => $this->_paymentData->getPaymentMethodList(true),
            'option_groups' => $this->_paymentData->getPaymentMethodList(true, true, true),
            'header_css_class' => 'col-method',
            'column_css_class' => 'col-method'
        ));

        $this->addColumn('txn_type', array(
            'header' => __('Transaction Type'),
            'index' => 'txn_type',
            'type' => 'options',
            'options' => \Mage::getSingleton('Magento\Sales\Model\Order\Payment\Transaction')->getTransactionTypes(),
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
     * @param $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return $this->getUrl('*/*/view', array('txn_id' => $item->getId()));
    }
}
