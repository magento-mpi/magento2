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
namespace Magento\Adminhtml\Block\Customer\Edit\Tab;

class View
 extends \Magento\Adminhtml\Block\Template
 implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
    protected $_customer;

    protected $_customerLog;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
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
            return \Mage::getModel('Magento\Customer\Model\Group')
                ->load($groupId)
                ->getCustomerGroupCode();
        }
    }

    /**
     * Load Customer Log model
     *
     * @return \Magento\Log\Model\Customer
     */
    public function getCustomerLog()
    {
        if (!$this->_customerLog) {
            $this->_customerLog = \Mage::getModel('Magento\Log\Model\Customer')
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
            \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM,
            true
        );
    }

    public function getStoreCreateDate()
    {
        $date = \Mage::app()->getLocale()->storeDate(
            $this->getCustomer()->getStoreId(),
            $this->getCustomer()->getCreatedAtTimestamp(),
            true
        );
        return $this->formatDate($date, \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM, true);
    }

    public function getStoreCreateDateTimezone()
    {
        return \Mage::app()->getStore($this->getCustomer()->getStoreId())
            ->getConfig(\Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_TIMEZONE);
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
                \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM,
                true
            );
        }
        return __('Never');
    }

    public function getStoreLastLoginDate()
    {
        $date = $this->getCustomerLog()->getLoginAtTimestamp();
        if ($date) {
            $date = \Mage::app()->getLocale()->storeDate(
                $this->getCustomer()->getStoreId(),
                $date,
                true
            );
            return $this->formatDate($date, \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM, true);
        }
        return __('Never');
    }

    public function getStoreLastLoginDateTimezone()
    {
        return \Mage::app()->getStore($this->getCustomer()->getStoreId())
            ->getConfig(\Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_TIMEZONE);
    }

    public function getCurrentStatus()
    {
        $log = $this->getCustomerLog();
        $interval = \Magento\Log\Model\Visitor::getOnlineMinutesInterval();
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
        return \Mage::app()->getStore($this->getCustomer()->getStoreId())->getName();
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
