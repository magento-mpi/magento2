<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml paypal settlement reports grid block
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Adminhtml_Settlement_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Retain filter parameters in session
     *
     * @var bool
     */
    protected $_saveParametersInSession = true;

    /**
     * Constructor
     * Set main configuration of grid
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('settlementGrid');
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for grid
     * @return Mage_Paypal_Block_Adminhtml_Settlement_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Mage_Paypal_Model_Resource_Report_Settlement_Row_Collection');
        $this->setCollection($collection);
        if (!$this->getParam($this->getVarNameSort())) {
            $collection->setOrder('row_id', 'desc');
        }
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     * @return Mage_Paypal_Block_Adminhtml_Settlement_Grid
     */
    protected function _prepareColumns()
    {
        $settlement = Mage::getSingleton('Mage_Paypal_Model_Report_Settlement');
        $this->addColumn('report_date', array(
            'header'    => $settlement->getFieldLabel('report_date'),
            'index'     => 'report_date',
            'type'     => 'date',
            'header_css_class'  => 'col-date',
            'column_css_class'  => 'col-date'
        ));
        $this->addColumn('account_id', array(
            'header'    => $settlement->getFieldLabel('account_id'),
            'index'     => 'account_id',
            'header_css_class'  => 'col-merchant',
            'column_css_class'  => 'col-merchant'
        ));
        $this->addColumn('transaction_id', array(
            'header'    => $settlement->getFieldLabel('transaction_id'),
            'index'     => 'transaction_id',
            'header_css_class'  => 'col-transaction',
            'column_css_class'  => 'col-transaction'
        ));
        $this->addColumn('invoice_id', array(
            'header'    => $settlement->getFieldLabel('invoice_id'),
            'index'     => 'invoice_id',
            'header_css_class'  => 'col-invoice',
            'column_css_class'  => 'col-invoice'
        ));
        $this->addColumn('paypal_reference_id', array(
            'header'    => $settlement->getFieldLabel('paypal_reference_id'),
            'index'     => 'paypal_reference_id',
            'header_css_class'  => 'col-reference',
            'column_css_class'  => 'col-reference'
        ));
        $this->addColumn('transaction_event_code', array(
            'header'    => $settlement->getFieldLabel('transaction_event'),
            'index'     => 'transaction_event_code',
            'type'      => 'options',
            'options'   => Mage::getModel('Mage_Paypal_Model_Report_Settlement_Row')->getTransactionEvents(),
            'header_css_class'  => 'col-event',
            'column_css_class'  => 'col-event'
        ));
        $this->addColumn('transaction_initiation_date', array(
            'header'    => $settlement->getFieldLabel('transaction_initiation_date'),
            'index'     => 'transaction_initiation_date',
            'type'      => 'datetime',
            'header_css_class'  => 'col-initiation',
            'column_css_class'  => 'col-initiation'
        ));
        $this->addColumn('transaction_completion_date', array(
            'header'    => $settlement->getFieldLabel('transaction_completion_date'),
            'index'     => 'transaction_completion_date',
            'type'      => 'datetime',
            'header_css_class'  => 'col-completion',
            'column_css_class'  => 'col-completion'
        ));
        $this->addColumn('gross_transaction_amount', array(
            'header'    => $settlement->getFieldLabel('gross_transaction_amount'),
            'index'     => 'gross_transaction_amount',
            'type'      => 'currency',
            'currency'  => 'gross_transaction_currency',
            'header_css_class'  => 'col-amount',
            'column_css_class'  => 'col-amount'
        ));
        $this->addColumn('fee_amount', array(
            'header'    => $settlement->getFieldLabel('fee_amount'),
            'index'     => 'fee_amount',
            'type'      => 'currency',
            'currency'  => 'gross_transaction_currency',
            'header_css_class'  => 'col-fee-amount',
            'column_css_class'  => 'col-fee-amount'
        ));
        return parent::_prepareColumns();
    }

    /**
     * Return grid URL
     * @return string
     */
    public function getGridUrl()
    {
         return $this->getUrl('*/*/grid');
    }

    /**
     * Return item view URL
     * @return string
     */
    public function getRowUrl($item)
    {
        return $this->getUrl('*/*/details', array('id' => $item->getId()));
    }
}