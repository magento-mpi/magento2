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
 * Customer account form block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Edit_Tab_View
    extends Magento_Adminhtml_Block_Template
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_customer;

    protected $_customerLog;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @var Magento_Log_Model_Visitor
     */
    protected $_modelVisitor;

    /**
     * @var Magento_Customer_Model_GroupFactory
     */
    protected $_groupFactory;

    /**
     * @var Magento_Log_Model_CustomerFactory
     */
    protected $_logFactory;

    /**
     * @param Magento_Customer_Model_GroupFactory $groupFactory
     * @param Magento_Log_Model_CustomerFactory $logFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Log_Model_Visitor $modelVisitor
     * @param array $data
     */
    public function __construct(
        Magento_Customer_Model_GroupFactory $groupFactory,
        Magento_Log_Model_CustomerFactory $logFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Log_Model_Visitor $modelVisitor,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_modelVisitor = $modelVisitor;
        $this->_groupFactory = $groupFactory;
        $this->_logFactory = $logFactory;
        parent::__construct($coreData, $context, $data);
    }

    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = $this->_coreRegistry->registry('current_customer');
        }
        return $this->_customer;
    }

    public function getGroupName()
    {
        $groupId = $this->getCustomer()->getGroupId();
        if ($groupId) {
            return $this->_groupFactory->create()
                ->load($groupId)
                ->getCustomerGroupCode();
        }
    }

    /**
     * Load Customer Log model
     *
     * @return Magento_Log_Model_Customer
     */
    public function getCustomerLog()
    {
        if (!$this->_customerLog) {
            $this->_customerLog = $this->_logFactory->create()
                ->loadByCustomer($this->getCustomer()->getId());
        }
        return $this->_customerLog;
    }

    /**
     * Get customer creation date
     *
     * @return string
     */
    public function getCreateDate()
    {
        return $this->_coreData->formatDate(
            $this->getCustomer()->getCreatedAtTimestamp(),
            Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM,
            true
        );
    }

    public function getStoreCreateDate()
    {
        $date = $this->_locale->storeDate(
            $this->getCustomer()->getStoreId(),
            $this->getCustomer()->getCreatedAtTimestamp(),
            true
        );
        return $this->formatDate($date, Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM, true);
    }

    public function getStoreCreateDateTimezone()
    {
        return $this->_storeConfig->getConfig(
            Magento_Core_Model_LocaleInterface::XML_PATH_DEFAULT_TIMEZONE,
            $this->getCustomer()->getStoreId()
        );
    }

    /**
     * Get customer last login date
     *
     * @return string
     */
    public function getLastLoginDate()
    {
        $date = $this->getCustomerLog()->getLoginAtTimestamp();
        if ($date) {
            return $this->_coreData->formatDate(
                $date,
                Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM,
                true
            );
        }
        return __('Never');
    }

    public function getStoreLastLoginDate()
    {
        $date = $this->getCustomerLog()->getLoginAtTimestamp();
        if ($date) {
            $date = $this->_locale->storeDate(
                $this->getCustomer()->getStoreId(),
                $date,
                true
            );
            return $this->formatDate($date, Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM, true);
        }
        return __('Never');
    }

    public function getStoreLastLoginDateTimezone()
    {
        return $this->_storeConfig->getConfig(
            Magento_Core_Model_LocaleInterface::XML_PATH_DEFAULT_TIMEZONE,
            $this->getCustomer()->getStoreId()
        );
    }

    public function getCurrentStatus()
    {
        $log = $this->getCustomerLog();
        $interval = $this->_modelVisitor->getOnlineMinutesInterval();
        if ($log->getLogoutAt() || (strtotime(now()) - strtotime($log->getLastVisitAt()) > $interval * 60)) {
            return __('Offline');
        }
        return __('Online');
    }

    public function getIsConfirmedStatus()
    {
        $this->getCustomer();
        if (!$this->_customer->getConfirmation()) {
            return __('Confirmed');
        }
        if ($this->_customer->isConfirmationRequired()) {
            return __('Not confirmed, cannot login');
        }
        return __('Not confirmed, can login');
    }

    public function getCreatedInStore()
    {
        return $this->_storeManager->getStore($this->getCustomer()->getStoreId())->getName();
    }

    public function getStoreId()
    {
        return $this->getCustomer()->getStoreId();
    }

    public function getBillingAddressHtml()
    {
        if ($address = $this->getCustomer()->getPrimaryBillingAddress()) {
            return $address->format('html');
        }
        return __('The customer does not have default billing address.');
    }

    public function getAccordionHtml()
    {
        return $this->getChildHtml('accordion');
    }

    public function getSalesHtml()
    {
        return $this->getChildHtml('sales');
    }

    public function getTabLabel()
    {
        return __('Customer View');
    }

    public function getTabTitle()
    {
        return __('Customer View');
    }

    public function canShowTab()
    {
        if ($this->_coreRegistry->registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        if ($this->_coreRegistry->registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }
}
