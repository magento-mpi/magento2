<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation reports controller
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */

class Enterprise_Invitation_Adminhtml_Report_InvitationController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init action breadcrumbs
     *
     * @return Enterprise_Invitation_Adminhtml_Report_InvitationController
     */
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('Mage_Reports_Helper_Data')->__('Reports'), Mage::helper('Mage_Reports_Helper_Data')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('Enterprise_Invitation_Helper_Data')->__('Invitations'), Mage::helper('Enterprise_Invitation_Helper_Data')->__('Invitations'));
        return $this;
    }

    /**
     * General report action
     */
    public function indexAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Invitations'))
             ->_title($this->__('General'));

        $this->_initAction()
            ->_setActiveMenu('report/enterprise_invitation/general')
            ->_addBreadcrumb(Mage::helper('Enterprise_Invitation_Helper_Data')->__('General Report'), Mage::helper('Enterprise_Invitation_Helper_Data')->__('General Report'))
            ->_addContent(
                $this->getLayout()->createBlock('Enterprise_Invitation_Block_Adminhtml_Report_Invitation_General')
            )
            ->renderLayout();
    }

    /**
     * Export invitation general report grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'invitation_general.csv';
        $content    = $this->getLayout()
            ->createBlock('Enterprise_Invitation_Block_Adminhtml_Report_Invitation_General_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export invitation general report grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'invitation_general.xml';
        $content    = $this->getLayout()
            ->createBlock('Enterprise_Invitation_Block_Adminhtml_Report_Invitation_General_Grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Report by customers action
     */
    public function customerAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Invitations'))
             ->_title($this->__('Customers'));

        $this->_initAction()
            ->_setActiveMenu('report/enterprise_invitation/customer')
            ->_addBreadcrumb(Mage::helper('Enterprise_Invitation_Helper_Data')->__('Invitation Report by Customers'), Mage::helper('Enterprise_Invitation_Helper_Data')->__('Invitation Report by Customers'))
            ->_addContent(
                $this->getLayout()->createBlock('Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Customer')
            )
            ->renderLayout();
    }

    /**
     * Export invitation customer report grid to CSV format
     */
    public function exportCustomerCsvAction()
    {
        $fileName   = 'invitation_customer.csv';
        $content    = $this->getLayout()
            ->createBlock('Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Customer_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export invitation customer report grid to Excel XML format
     */
    public function exportCustomerExcelAction()
    {
        $fileName   = 'invitation_customer.xml';
        $content    = $this->getLayout()
            ->createBlock('Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Customer_Grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Report by order action
     */
    public function orderAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Invitations'))
             ->_title($this->__('Order Conversion Rate'));

        $this->_initAction()
            ->_setActiveMenu('report/enterprise_invitation/order')
            ->_addBreadcrumb(Mage::helper('Enterprise_Invitation_Helper_Data')->__('Invitation Report by Customers'), Mage::helper('Enterprise_Invitation_Helper_Data')->__('Invitation Report by Order Conversion Rate'))
            ->_addContent(
                $this->getLayout()->createBlock('Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Order')
            )
            ->renderLayout();
    }

    /**
     * Export invitation order report grid to CSV format
     */
    public function exportOrderCsvAction()
    {
        $fileName   = 'invitation_order.csv';
        $content    = $this->getLayout()
            ->createBlock('Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Order_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export invitation order report grid to Excel XML format
     */
    public function exportOrderExcelAction()
    {
        $fileName   = 'invitation_order.xml';
        $content    = $this->getLayout()
            ->createBlock('Enterprise_Invitation_Block_Adminhtml_Report_Invitation_Order_Grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Acl admin user check
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Enterprise_Invitation_Model_Config')->isEnabled() &&
               Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('report/enterprise_invitation');
    }
}
