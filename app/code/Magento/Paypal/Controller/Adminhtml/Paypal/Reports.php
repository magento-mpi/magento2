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
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paypal\Controller\Adminhtml\Paypal;

class Reports extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

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
        $row = \Mage::getModel('Magento\Paypal\Model\Report\Settlement\Row')->load($rowId);
        if (!$row->getId()) {
            $this->_redirect('*/*/');
            return;
        }
        $this->_coreRegistry->register('current_transaction', $row);
        $this->_initAction()
            ->_title(__('View Transaction'))
            ->_addContent($this->getLayout()
                ->createBlock('Magento\Paypal\Block\Adminhtml\Settlement\Details', 'settlementDetails'))
            ->renderLayout();
    }

    /**
     * Forced fetch reports action
     */
    public function fetchAction()
    {
        try {
            $reports = \Mage::getModel('Magento\Paypal\Model\Report\Settlement');
            /* @var $reports \Magento\Paypal\Model\Report\Settlement */
            $credentials = $reports->getSftpCredentials();
            if (empty($credentials)) {
                \Mage::throwException(__('We found nothing to fetch because of an empty configuration.'));
            }
            foreach ($credentials as $config) {
                try {
                    $fetched = $reports->fetchAndSave(\Magento\Paypal\Model\Report\Settlement::createConnection($config));
                    $this->_getSession()->addSuccess(
                        __("We fetched %1 report rows from '%2@%3'.", $fetched, $config['username'], $config['hostname'])
                    );
                } catch (\Exception $e) {
                    $this->_getSession()->addError(
                        __("We couldn't fetch reports from '%1@%2'.", $config['username'], $config['hostname'])
                    );
                    $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
                }
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Initialize titles, navigation
     * @return \Magento\Paypal\Controller\Adminhtml\Paypal\Reports
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
                break;
            case 'fetch':
                return $this->_authorization->isAllowed('Magento_Paypal::fetch');
                break;
            default:
                return $this->_authorization->isAllowed('Magento_Paypal::paypal_settlement_reports');
                break;
        }
    }
}
