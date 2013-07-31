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
 * PayPal Settlement Reports Controller
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Controller_Adminhtml_Paypal_Reports extends Magento_Adminhtml_Controller_Action
{
    /**
     * Grid action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Ajax callback for grid actions
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * View transaction details action
     */
    public function detailsAction()
    {
        $rowId = $this->getRequest()->getParam('id');
        $row = Mage::getModel('Mage_Paypal_Model_Report_Settlement_Row')->load($rowId);
        if (!$row->getId()) {
            $this->_redirect('*/*/');
            return;
        }
        Mage::register('current_transaction', $row);
        $this->_initAction()
            ->_title($this->__('View Transaction'))
            ->_addContent($this->getLayout()
                ->createBlock('Mage_Paypal_Block_Adminhtml_Settlement_Details', 'settlementDetails'))
            ->renderLayout();
    }

    /**
     * Forced fetch reports action
     */
    public function fetchAction()
    {
        try {
            $reports = Mage::getModel('Mage_Paypal_Model_Report_Settlement');
            /* @var $reports Mage_Paypal_Model_Report_Settlement */
            $credentials = $reports->getSftpCredentials();
            if (empty($credentials)) {
                Mage::throwException(Mage::helper('Mage_Paypal_Helper_Data')->__('We found nothing to fetch because of an empty configuration.'));
            }
            foreach ($credentials as $config) {
                try {
                    $fetched = $reports->fetchAndSave(Mage_Paypal_Model_Report_Settlement::createConnection($config));
                    $this->_getSession()->addSuccess(
                        Mage::helper('Mage_Paypal_Helper_Data')->__("We fetched %s report rows from '%s@%s'.", $fetched, $config['username'], $config['hostname'])
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError(
                        Mage::helper('Mage_Paypal_Helper_Data')->__("We couldn't fetch reports from '%s@%s'.", $config['username'], $config['hostname'])
                    );
                    Mage::logException($e);
                }
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Initialize titles, navigation
     * @return Mage_Paypal_Controller_Adminhtml_Paypal_Reports
     */
    protected function _initAction()
    {
        $this->_title($this->__('PayPal Settlement Reports'));
        $this->loadLayout()
            ->_setActiveMenu('Mage_Paypal::report_salesroot_paypal_settlement_reports')
            ->_addBreadcrumb(Mage::helper('Mage_Paypal_Helper_Data')->__('Reports'), Mage::helper('Mage_Paypal_Helper_Data')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('Mage_Paypal_Helper_Data')->__('Sales'), Mage::helper('Mage_Paypal_Helper_Data')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('Mage_Paypal_Helper_Data')->__('PayPal Settlement Reports'), Mage::helper('Mage_Paypal_Helper_Data')->__('PayPal Settlement Reports'));
        return $this;
    }

    /**
     * ACL check
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'index':
            case 'details':
                return $this->_authorization->isAllowed('Mage_Paypal::paypal_settlement_reports_view');
                break;
            case 'fetch':
                return $this->_authorization->isAllowed('Mage_Paypal::fetch');
                break;
            default:
                return $this->_authorization->isAllowed('Mage_Paypal::paypal_settlement_reports');
                break;
        }
    }
}
