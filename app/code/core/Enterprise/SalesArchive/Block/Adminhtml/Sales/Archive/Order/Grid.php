<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Archive orders grid block
 *
 */

class Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort(false);
        $this->setId('sales_order_grid_archive');
    }


    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Enterprise_SalesArchive_Model_Resource_Order_Collection';
    }

    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
         return $this->getUrl('*/*/ordersgrid', array('_current' => true));
    }

    /**
     * Init sales archive massactions
     *
     * @return Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Grid
     */
    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
        $this->_rssLists = array();

        if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_order', array(
                 'label'=> Mage::helper('Mage_Sales_Helper_Data')->__('Cancel'),
                 'url'  => $this->getUrl('*/sales_archive/massCancel'),
            ));
        }

        if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/order/actions/hold')) {
            $this->getMassactionBlock()->addItem('hold_order', array(
                 'label'=> Mage::helper('Mage_Sales_Helper_Data')->__('Hold'),
                 'url'  => $this->getUrl('*/sales_archive/massHold'),
            ));
        }

        if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/order/actions/unhold')) {
            $this->getMassactionBlock()->addItem('unhold_order', array(
                 'label'=> Mage::helper('Mage_Sales_Helper_Data')->__('Unhold'),
                 'url'  => $this->getUrl('*/sales_archive/massUnhold'),
            ));
        }

        if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/archive/order/remove')) {
            $this->getMassactionBlock()->addItem('remove_order_from_archive', array(
                 'label'=> Mage::helper('Enterprise_SalesArchive_Helper_Data')->__('Move to Orders Management'),
                 'url'  => $this->getUrl('*/sales_archive/massRemove'),
            ));
        }

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label'=> Mage::helper('Mage_Sales_Helper_Data')->__('Print Invoices'),
             'url'  => $this->getUrl('*/sales_archive/massPrintInvoices'),
        ));

        $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
             'label'=> Mage::helper('Mage_Sales_Helper_Data')->__('Print Credit Memos'),
             'url'  => $this->getUrl('*/sales_archive/massPrintCreditMemos'),
        ));

        $this->getMassactionBlock()->addItem('pdfdocs_order', array(
             'label'=> Mage::helper('Mage_Sales_Helper_Data')->__('Print All'),
             'url'  => $this->getUrl('*/sales_archive/massPrintAllDocuments'),
        ));

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label'=> Mage::helper('Mage_Sales_Helper_Data')->__('Print Packingslips'),
             'url'  => $this->getUrl('*/sales_archive/massPrintPackingSlips'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label'=> Mage::helper('Mage_Sales_Helper_Data')->__('Print Shipping Labels'),
             'url'  => $this->getUrl('*/sales_archive/massPrintShippingLabel'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/archive/orders')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }
}
