<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer account form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_View
 extends Mage_Adminhtml_Block_Template
 implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_customer;

    protected $_customerLog;

    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = Mage::registry('current_customer');
        }
        return $this->_customer;
    }

    public function getGroupName()
    {
        if ($groupId = $this->getCustomer()->getGroupId()) {
            return Mage::getModel('Mage_Customer_Model_Group')
                ->load($groupId)
                ->getCustomerGroupCode();
        }
    }

    /**
     * Load Customer Log model
     *
     * @return Mage_Log_Model_Customer
     */
    public function getCustomerLog()
    {
        if (!$this->_customerLog) {
            $this->_customerLog = Mage::getModel('Mage_Log_Model_Customer')
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
        return Mage::helper('Mage_Core_Helper_Data')->formatDate(
            $this->getCustomer()->getCreatedAtTimestamp(),
            Mage_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM,
            true
        );
    }

    public function getStoreCreateDate()
    {
        $date = Mage::app()->getLocale()->storeDate(
            $this->getCustomer()->getStoreId(),
            $this->getCustomer()->getCreatedAtTimestamp(),
            true
        );
        return $this->formatDate($date, Mage_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM, true);
    }

    public function getStoreCreateDateTimezone()
    {
        return Mage::app()->getStore($this->getCustomer()->getStoreId())
            ->getConfig(Mage_Core_Model_LocaleInterface::XML_PATH_DEFAULT_TIMEZONE);
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
            return Mage::helper('Mage_Core_Helper_Data')->formatDate(
                $date,
                Mage_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM,
                true
            );
        }
        return __('Never');
    }

    public function getStoreLastLoginDate()
    {
        if ($date = $this->getCustomerLog()->getLoginAtTimestamp()) {
            $date = Mage::app()->getLocale()->storeDate(
                $this->getCustomer()->getStoreId(),
                $date,
                true
            );
            return $this->formatDate($date, Mage_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM, true);
        }
        return __('Never');
    }

    public function getStoreLastLoginDateTimezone()
    {
        return Mage::app()->getStore($this->getCustomer()->getStoreId())
            ->getConfig(Mage_Core_Model_LocaleInterface::XML_PATH_DEFAULT_TIMEZONE);
    }

    public function getCurrentStatus()
    {
        $log = $this->getCustomerLog();
        if ($log->getLogoutAt() ||
            strtotime(now())-strtotime($log->getLastVisitAt())>Mage_Log_Model_Visitor::getOnlineMinutesInterval()*60) {
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
        return Mage::app()->getStore($this->getCustomer()->getStoreId())->getName();
    }

    public function getStoreId()
    {
        return $this->getCustomer()->getStoreId();
    }

    public function getBillingAddressHtml()
    {
        $html = '';
        if ($address = $this->getCustomer()->getPrimaryBillingAddress()) {
            $html = $address->format('html');
        }
        else {
            $html = __('The customer does not have default billing address.');
        }
        return $html;
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
        if (Mage::registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        if (Mage::registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }

}
