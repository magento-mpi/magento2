<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal Settlement Reports Controller
 */
class Magento_Paypal_Controller_Adminhtml_Paypal_Reports extends Magento_Adminhtml_Controller_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Paypal_Model_Report_Settlement_RowFactory
     */
    protected $_rowFactory;

    /**
     * @var Magento_Paypal_Model_Report_SettlementFactory
     */
    protected $_settlementFactory;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Paypal_Model_Report_Settlement_RowFactory $rowFactory
     * @param Magento_Paypal_Model_Report_SettlementFactory $settlementFactory
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Paypal_Model_Report_Settlement_RowFactory $rowFactory,
        Magento_Paypal_Model_Report_SettlementFactory $settlementFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_rowFactory = $rowFactory;
        $this->_settlementFactory = $settlementFactory;
        $this->_logger = $context->getLogger();
        parent::__construct($context);
    }

    /**
     * Grid action
     */
    public function indexAction()
    {
        $this->_initAction()->renderLayout();
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
        $row = $this->_rowFactory->create()->load($rowId);
        if (!$row->getId()) {
            $this->_redirect('*/*/');
            return;
        }
        $this->_coreRegistry->register('current_transaction', $row);
        $this->_initAction()
            ->_title(__('View Transaction'))
            ->_addContent($this->getLayout()
                ->createBlock('Magento_Paypal_Block_Adminhtml_Settlement_Details', 'settlementDetails'))
            ->renderLayout();
    }

    /**
     * Forced fetch reports action
     *
     * @throws Magento_Core_Exception
     */
    public function fetchAction()
    {
        try {
            $reports = $this->_settlementFactory->create();
            /* @var $reports Magento_Paypal_Model_Report_Settlement */
            $credentials = $reports->getSftpCredentials();
            if (empty($credentials)) {
                throw new Magento_Core_Exception(__('We found nothing to fetch because of an empty configuration.'));
            }
            foreach ($credentials as $config) {
                try {
                    $fetched = $reports->fetchAndSave(
                        Magento_Paypal_Model_Report_Settlement::createConnection($config)
                    );
                    $this->_getSession()->addSuccess(
                        __("We fetched %1 report rows from '%2@%3'.", $fetched,
                            $config['username'], $config['hostname'])
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError(
                        __("We couldn't fetch reports from '%1@%2'.", $config['username'], $config['hostname'])
                    );
                    $this->_logger->logException($e);
                }
            }
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_logger->logException($e);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Initialize titles, navigation
     * @return Magento_Paypal_Controller_Adminhtml_Paypal_Reports
     */
    protected function _initAction()
    {
        $this->_title(__('PayPal Settlement Reports'));
        $this->loadLayout()
            ->_setActiveMenu('Magento_Paypal::report_salesroot_paypal_settlement_reports')
            ->_addBreadcrumb(__('Reports'), __('Reports'))
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('PayPal Settlement Reports'), __('PayPal Settlement Reports'));
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
                return $this->_authorization->isAllowed('Magento_Paypal::paypal_settlement_reports_view');
            case 'fetch':
                return $this->_authorization->isAllowed('Magento_Paypal::fetch');
            default:
                return $this->_authorization->isAllowed('Magento_Paypal::paypal_settlement_reports');
        }
    }
}
