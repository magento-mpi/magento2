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
 * Report statistics admin controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Report_Statistics extends Magento_Adminhtml_Controller_Action
{
    /**
     * Admin session model
     *
     * @var null|Mage_Backend_Model_Auth_Session
     */
    protected $_adminSession = null;

    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('Magento_Reports_Helper_Data')->__('Reports'), Mage::helper('Magento_Reports_Helper_Data')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('Magento_Reports_Helper_Data')->__('Statistics'), Mage::helper('Magento_Reports_Helper_Data')->__('Statistics'));
        return $this;
    }

    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }

        $requestData = Mage::helper('Magento_Adminhtml_Helper_Data')->prepareFilterString($this->getRequest()->getParam('filter'));
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
     * Retrieve array of collection names by code specified in request
     *
     * @return array
     */
    protected function _getCollectionNames()
    {
        $codes = $this->getRequest()->getParam('code');
        if (!$codes) {
            throw new Exception(Mage::helper('Magento_Adminhtml_Helper_Data')->__('No report code is specified.'));
        }

        if(!is_array($codes) && strpos($codes, ',') === false) {
            $codes = array($codes);
        } elseif (!is_array($codes)) {
            $codes = explode(',', $codes);
        }

        $aliases = array(
            'sales'       => 'Mage_Sales_Model_Resource_Report_Order',
            'tax'         => 'Mage_Tax_Model_Resource_Report_Tax',
            'shipping'    => 'Mage_Sales_Model_Resource_Report_Shipping',
            'invoiced'    => 'Mage_Sales_Model_Resource_Report_Invoiced',
            'refunded'    => 'Mage_Sales_Model_Resource_Report_Refunded',
            'coupons'     => 'Mage_SalesRule_Model_Resource_Report_Rule',
            'bestsellers' => 'Mage_Sales_Model_Resource_Report_Bestsellers',
            'viewed'      => 'Magento_Reports_Model_Resource_Report_Product_Viewed',
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
     * @return Magento_Adminhtml_Controller_Report_Sales
     */
    public function refreshRecentAction()
    {
        try {
            $collectionsNames = $this->_getCollectionNames();
            $currentDate = Mage::app()->getLocale()->date();
            $date = $currentDate->subHour(25);
            foreach ($collectionsNames as $collectionName) {
                Mage::getResourceModel($collectionName)->aggregate($date);
            }
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Recent statistics have been updated.'));
        } catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(Mage::helper('Magento_Adminhtml_Helper_Data')->__('We can\'t refresh recent statistics.'));
            Mage::logException($e);
        }

        if($this->_getSession()->isFirstPageAfterLogin()) {
            $this->_redirect('*/*');
        } else {
            $this->_redirectReferer('*/*');
        }
        return $this;
    }

    /**
     * Refresh statistics for all period
     *
     * @return Magento_Adminhtml_Controller_Report_Sales
     */
    public function refreshLifetimeAction()
    {
        try {
            $collectionsNames = $this->_getCollectionNames();
            foreach ($collectionsNames as $collectionName) {
                Mage::getResourceModel($collectionName)->aggregate();
            }
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(Mage::helper('Magento_Adminhtml_Helper_Data')->__('We updated lifetime statistics.'));
        } catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(Mage::helper('Magento_Adminhtml_Helper_Data')->__('We can\'t refresh lifetime statistics.'));
            Mage::logException($e);
        }

        if($this->_getSession()->isFirstPageAfterLogin()) {
            $this->_redirect('*/*');
        } else {
            $this->_redirectReferer('*/*');
        }

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Refresh Statistics'));

        $this->_initAction()
            ->_setActiveMenu('Magento_Reports::report_statistics_refresh')
            ->_addBreadcrumb(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Refresh Statistics'), Mage::helper('Magento_Adminhtml_Helper_Data')->__('Refresh Statistics'))
            ->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Reports::statistics');
    }

    /**
     * Retrieve admin session model
     *
     * @return Mage_Backend_Model_Auth_Session
     */
    protected function _getSession()
    {
        if (is_null($this->_adminSession)) {
            $this->_adminSession = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        }
        return $this->_adminSession;
    }
}
