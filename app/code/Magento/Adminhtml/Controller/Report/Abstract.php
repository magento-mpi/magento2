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
abstract class Magento_Adminhtml_Controller_Report_Abstract extends Magento_Adminhtml_Controller_Action
{
    /**
     * Admin session model
     *
     * @var null|Magento_Backend_Model_Auth_Session
     */
    protected $_adminSession = null;

    /**
     * Retrieve admin session model
     *
     * @return Magento_Backend_Model_Auth_Session
     */
    protected function _getSession()
    {
        if (is_null($this->_adminSession)) {
            $this->_adminSession = Mage::getSingleton('Magento_Backend_Model_Auth_Session');
        }
        return $this->_adminSession;
    }

    /**
     * Add report breadcrumbs
     *
     * @return Magento_Adminhtml_Controller_Report_Abstract
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
     * @param array|Magento_Object $blocks
     * @return Magento_Adminhtml_Controller_Report_Abstract
     */
    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $requestData = $this->_objectManager->get('Magento_Adminhtml_Helper_Data')
            ->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData = $this->_filterDates($requestData, array('from', 'to'));
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new Magento_Object();

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
     * @return Magento_Adminhtml_Controller_Report_Abstract
     */
    protected function _showLastExecutionTime($flagCode, $refreshCode)
    {
        $flag = Mage::getModel('Magento_Reports_Model_Flag')->setReportFlagCode($flagCode)->loadSelf();
        $updatedAt = ($flag->hasData())
            ? Mage::app()->getLocale()->storeDate(
                0, new Zend_Date($flag->getLastUpdate(), Magento_Date::DATETIME_INTERNAL_FORMAT), true
            )
            : 'undefined';

        $refreshStatsLink = $this->getUrl('*/report_statistics');
        $directRefreshLink = $this->getUrl('*/report_statistics/refreshRecent', array('code' => $refreshCode));

        Mage::getSingleton('Magento_Adminhtml_Model_Session')
            ->addNotice(__('Last updated: %1. To refresh last day\'s <a href="%2">statistics</a>, '
                . 'click <a href="%3">here</a>.', $updatedAt, $refreshStatsLink, $directRefreshLink));
        return $this;
    }
}
