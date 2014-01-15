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
 * Admin abstract reports controller
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Controller\Adminhtml\Report;

abstract class AbstractReport extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Core\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Core\Filter\Date $dateFilter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\App\Response\Http\FileFactory $fileFactory,
        \Magento\Core\Filter\Date $dateFilter
    ) {
        parent::__construct($context);
        $this->_fileFactory = $fileFactory;
        $this->_dateFilter = $dateFilter;
    }

    /**
     * Admin session model
     *
     * @var null|\Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession = null;

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

    /**
     * Add report breadcrumbs
     *
     * @return \Magento\Reports\Controller\Adminhtml\Report\AbstractReport
     */
    public function _initAction()
    {
        $this->_view->loadLayout();
        $this->_addBreadcrumb(__('Reports'), __('Reports'));
        return $this;
    }

    /**
     * Report action init operations
     *
     * @param array|\Magento\Object $blocks
     * @return \Magento\Reports\Controller\Adminhtml\Report\AbstractReport
     */
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
     * Add refresh statistics links
     *
     * @param string $flagCode
     * @param string $refreshCode
     * @return \Magento\Reports\Controller\Adminhtml\Report\AbstractReport
     */
    protected function _showLastExecutionTime($flagCode, $refreshCode)
    {
        $flag = $this->_objectManager->create('Magento\Reports\Model\Flag')->setReportFlagCode($flagCode)->loadSelf();
        $updatedAt = ($flag->hasData())
            ? $this->_objectManager->get('Magento\Core\Model\LocaleInterface')->storeDate(
                0, new \Zend_Date($flag->getLastUpdate(), \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT), true
            )
            : 'undefined';

        $refreshStatsLink = $this->getUrl('reports/report_statistics');
        $directRefreshLink = $this->getUrl('reports/report_statistics/refreshRecent', array('code' => $refreshCode));

        $this->messageManager
            ->addNotice(__('Last updated: %1. To refresh last day\'s <a href="%2">statistics</a>, '
                . 'click <a href="%3">here</a>.', $updatedAt, $refreshStatsLink, $directRefreshLink));
        return $this;
    }
}
