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
 * Admin abstract reports controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Report;

abstract class AbstractReport extends \Magento\Adminhtml\Controller\Action
{
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
            $this->_adminSession = \Mage::getSingleton('Magento\Backend\Model\Auth\Session');
        }
        return $this->_adminSession;
    }

    /**
     * Add report breadcrumbs
     *
     * @return \Magento\Adminhtml\Controller\Report\AbstractReport
     */
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(__('Reports'), __('Reports'));
        return $this;
    }

    /**
     * Report action init operations
     *
     * @param array|\Magento\Object $blocks
     * @return \Magento\Adminhtml\Controller\Report\AbstractReport
     */
    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $requestData = \Mage::helper('Magento\Adminhtml\Helper\Data')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData = $this->_filterDates($requestData, array('from', 'to'));
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
     * @return \Magento\Adminhtml\Controller\Report\AbstractReport
     */
    protected function _showLastExecutionTime($flagCode, $refreshCode)
    {
        $flag = \Mage::getModel('Magento\Reports\Model\Flag')->setReportFlagCode($flagCode)->loadSelf();
        $updatedAt = ($flag->hasData())
            ? \Mage::app()->getLocale()->storeDate(
                0, new \Zend_Date($flag->getLastUpdate(), \Magento\Date::DATETIME_INTERNAL_FORMAT), true
            )
            : 'undefined';

        $refreshStatsLink = $this->getUrl('*/report_statistics');
        $directRefreshLink = $this->getUrl('*/report_statistics/refreshRecent', array('code' => $refreshCode));

        \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addNotice(__('Last updated: %1. To refresh last day\'s <a href="%2">statistics</a>, click <a href="%3">here</a>.', $updatedAt, $refreshStatsLink, $directRefreshLink));
        return $this;
    }
}
