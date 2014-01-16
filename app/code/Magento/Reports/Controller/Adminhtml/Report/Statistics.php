<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Report statistics admin controller
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Controller\Adminhtml\Report;

class Statistics extends \Magento\Backend\App\Action
{
    /**
     * Admin session model
     *
     * @var null|\Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession = null;

    /**
     * @var \Magento\Core\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Filter\Date $dateFilter
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Core\Filter\Date $dateFilter)
    {
        $this->_dateFilter = $dateFilter;
        parent::__construct($context);
    }

    public function _initAction()
    {
        $this->_view->loadLayout();
        $this->_addBreadcrumb(__('Reports'), __('Reports'));
        $this->_addBreadcrumb(__('Statistics'), __('Statistics'));
        return $this;
    }

    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $requestData = $this->_objectManager->get('Magento\Backend\Helper\Data')
            ->prepareFilterString($this->getRequest()->getParam('filter'));
        $inputFilter = new \Zend_Filter_Input(array('from' => $this->_dateFilter, 'to' => $this->_dateFilter),
            array(), $requestData);
        $requestData = $inputFilter->getUnescaped();
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new \Magento\Object();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }

        foreach ($blocks as $block) {
            if ($block) {
                $block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
            }
        }

        return $this;
    }

    /**
     * Retrieve array of collection names by code specified in request
     *
     * @return array
     */
    protected function _getCollectionNames()
    {
        $codes = $this->getRequest()->getParam('code');
        if (!$codes) {
            throw new \Exception(__('No report code is specified.'));
        }

        if(!is_array($codes) && strpos($codes, ',') === false) {
            $codes = array($codes);
        } elseif (!is_array($codes)) {
            $codes = explode(',', $codes);
        }

        $aliases = array(
            'sales'       => 'Magento\Sales\Model\Resource\Report\Order',
            'tax'         => 'Magento\Tax\Model\Resource\Report\Tax',
            'shipping'    => 'Magento\Sales\Model\Resource\Report\Shipping',
            'invoiced'    => 'Magento\Sales\Model\Resource\Report\Invoiced',
            'refunded'    => 'Magento\Sales\Model\Resource\Report\Refunded',
            'coupons'     => 'Magento\SalesRule\Model\Resource\Report\Rule',
            'bestsellers' => 'Magento\Sales\Model\Resource\Report\Bestsellers',
            'viewed'      => 'Magento\Reports\Model\Resource\Report\Product\Viewed',
        );
        $out = array();
        foreach ($codes as $code) {
            $out[] = $aliases[$code];
        }
        return $out;
    }

    /**
     * Refresh statistics for last 25 hours
     *
     * @return \Magento\Reports\Controller\Adminhtml\Report\Sales
     */
    public function refreshRecentAction()
    {
        try {
            $collectionsNames = $this->_getCollectionNames();
            $currentDate = $this->_objectManager->get('Magento\Core\Model\LocaleInterface')->date();
            $date = $currentDate->subHour(25);
            foreach ($collectionsNames as $collectionName) {
                $this->_objectManager->create($collectionName)->aggregate($date);
            }
            $this->messageManager->addSuccess(__('Recent statistics have been updated.'));
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t refresh recent statistics.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }

        if ($this->_getSession()->isFirstPageAfterLogin()) {
            $this->_redirect('adminhtml/*');
        } else {
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl('*/*'));
        }
    }

    /**
     * Refresh statistics for all period
     *
     * @return \Magento\Reports\Controller\Adminhtml\Report\Sales
     */
    public function refreshLifetimeAction()
    {
        try {
            $collectionsNames = $this->_getCollectionNames();
            foreach ($collectionsNames as $collectionName) {
                $this->_objectManager->create($collectionName)->aggregate();
            }
            $this->messageManager->addSuccess(__('We updated lifetime statistics.'));
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t refresh lifetime statistics.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }

        if($this->_getSession()->isFirstPageAfterLogin()) {
            $this->_redirect('adminhtml/*');
        } else {
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl('*/*'));
        }
    }

    public function indexAction()
    {
        $this->_title->add(__('Refresh Statistics'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_statistics_refresh')
            ->_addBreadcrumb(__('Refresh Statistics'), __('Refresh Statistics'));
        $this->_view->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Reports::statistics');
    }

    /**
     * Retrieve admin session model
     *
     * @return \Magento\Backend\Model\Auth\Session
     */
    protected function _getSession()
    {
        if (is_null($this->_adminSession)) {
            $this->_adminSession = $this->_objectManager->get('Magento\Backend\Model\Auth\Session');
        }
        return $this->_adminSession;
    }
}
