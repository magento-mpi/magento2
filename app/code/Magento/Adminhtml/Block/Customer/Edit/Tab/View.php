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

    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = \Mage::registry('current_customer');
        }
        return $this->_customer;
    }

    public function getGroupName()
    {
        if ($groupId = $this->getCustomer()->getGroupId()) {
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
        return \Mage::helper('Magento\Core\Helper\Data')->formatDate(
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
            return \Mage::helper('Magento\Core\Helper\Data')->formatDate(
                $date,
                \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM,
                true
            );
        }
        return __('Never');
    }

    public function getStoreLastLoginDate()
    {
        if ($date = $this->getCustomerLog()->getLoginAtTimestamp()) {
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
        if ($log->getLogoutAt() ||
            strtotime(now())-strtotime($log->getLastVisitAt())>\Magento\Log\Model\Visitor::getOnlineMinutesInterval()*60) {
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
        if (\Mage::registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        if (\Mage::registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }

}
