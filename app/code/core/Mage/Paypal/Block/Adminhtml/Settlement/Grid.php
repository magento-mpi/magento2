<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml paypal settlement reports grid block
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Adminhtml_Settlement_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     * Set main configuration of grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('settlementGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('transaction_completion_date', 'desc');
    }

    /**
     * Prepare collection for grid
     * @return Mage_Paypal_Block_Adminhtml_Settlement_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('paypal/report_settlement_row_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     * @return Mage_Paypal_Block_Adminhtml_Settlement_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('account_id', array(
            'header'    => Mage::helper('paypal')->__('Account ID'),
            'index'     => 'account_id'
        ));
        $this->addColumn('transaction_id', array(
            'header'    => Mage::helper('paypal')->__('Transaction ID'),
            'index'     => 'transaction_id'
        ));
        $this->addColumn('invoice_id', array(
            'header'    => Mage::helper('paypal')->__('Invoice ID'),
            'index'     => 'invoice_id'
        ));
        $this->addColumn('transaction_initiation_date', array(
            'header'    => Mage::helper('paypal')->__('Start Date'),
            'index'     => 'transaction_initiation_date',
            'type'      => 'date'
        ));
        $this->addColumn('transaction_completion_date', array(
            'header'    => Mage::helper('paypal')->__('Completion Date'),
            'index'     => 'transaction_completion_date',
            'type'      => 'date'
        ));

//        $this->addColumn('transaction_event_code', array(
//            'header'    => Mage::helper('paypal')->__('Transaction Event Code'),
//            'index'     => 'transaction_event_code'
//        ));

//        $this->addColumn('paypal_reference_id', array(
//            'header'    => Mage::helper('paypal')->__('Paypal Reference ID'),
//            'index'     => 'paypal_reference_id'
//        ));

//        $this->addColumn('paypal_reference_id_type', array(
//            'header'    => Mage::helper('paypal')->__('Reference ID Type'),
//            'index'     => 'paypal_reference_id_type',
//            'type'      => 'options',
//            'options'   => Mage::getModel('paypal/report_settlement_row')->getAvailableReferenceTypes()
//        ));

        $this->addColumn('transaction_debit_or_credit', array(
            'header'    => Mage::helper('paypal')->__('Transaction Debit/Credit'),
            'index'     => 'transaction_debit_or_credit',
            'type'      => 'options',
            'options'   => Mage::getModel('paypal/report_settlement_row')->getAvailableDebitCreditOptions()
        ));
        $this->addColumn('gross_transaction_currency', array(
            'header'    => Mage::helper('paypal')->__('Gross Currency'),
            'index'     => 'gross_transaction_currency',
        ));
        $this->addColumn('gross_transaction_amount', array(
            'header'    => Mage::helper('paypal')->__('Gross Amount'),
            'index'     => 'gross_transaction_amount',
        ));
        $this->addColumn('fee_debit_or_credit', array(
            'header'    => Mage::helper('paypal')->__('Fee Debit/Credit'),
            'index'     => 'fee_debit_or_credit',
            'type'      => 'options',
            'options'   => Mage::getModel('paypal/report_settlement_row')->getAvailableDebitCreditOptions()
        ));
        $this->addColumn('fee_currency', array(
            'header'    => Mage::helper('paypal')->__('Fee Currency'),
            'index'     => 'gross_transaction_currency',
        ));
        $this->addColumn('fee_amount', array(
            'header'    => Mage::helper('paypal')->__('Fee Amount'),
            'index'     => 'fee_amount',
        ));

//        $this->addColumn('custom_field', array(
//            'header'    => Mage::helper('paypal')->__('Custom Field'),
//            'index'     => 'custom_field',
//            'type'      => 'text'
//        ));

//        $this->addColumn('consumer_id', array(
//            'header'    => Mage::helper('paypal')->__('Consumer ID'),
//            'index'     => 'consumer_id',
//        ));

        //$this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        //$this->addExportType('*/*/exportXml', Mage::helper('customer')->__('XML'));
        return parent::_prepareColumns();
    }

    public function getMainButtonsHtml()
    {
        $fetchReportsButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('paypal')->__('Fetch Reports'),
                'onclick'   => "setLocation('" . $this->getUrl('*/*/fetch'). "')",
                'class'   => 'task'
            ))
        ;
        return parent::getMainButtonsHtml() . $fetchReportsButton->toHtml();
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
