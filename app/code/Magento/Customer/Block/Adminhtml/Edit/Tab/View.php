<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

/**
 * Customer account form block
 */
class View
    extends \Magento\Backend\Block\Template
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Log\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Log\Model\Customer
     */
    protected $_customerLog;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Log\Model\Visitor
     */
    protected $_modelVisitor;

    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $_groupFactory;

    /**
     * @var \Magento\Log\Model\CustomerFactory
     */
    protected $_logFactory;

    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Customer\Model\GroupFactory $groupFactory
     * @param \Magento\Log\Model\CustomerFactory $logFactory
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Log\Model\Visitor $modelVisitor
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\GroupFactory $groupFactory,
        \Magento\Log\Model\CustomerFactory $logFactory,
        \Magento\Core\Model\Registry $registry,
        \Magento\Log\Model\Visitor $modelVisitor,
        \Magento\Stdlib\DateTime $dateTime,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_modelVisitor = $modelVisitor;
        $this->_groupFactory = $groupFactory;
        $this->_logFactory = $logFactory;
        $this->dateTime = $dateTime;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Log\Model\Customer
     */
    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = $this->_coreRegistry->registry('current_customer');
        }
        return $this->_customer;
    }

    /**
     * @return int
     */
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
     * @return \Magento\Log\Model\Customer
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
        return $this->formatDate(
            $this->getCustomer()->getCreatedAt(),
            \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM,
            true
        );
    }

    /**
     * @return string
     */
    public function getStoreCreateDate()
    {
        $date = $this->_locale->storeDate(
            $this->getCustomer()->getStoreId(),
            $this->getCustomer()->getCreatedAtTimestamp(),
            true
        );
        return $this->formatDate($date, \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM, true);
    }

    /**
     * @return string
     */
    public function getStoreCreateDateTimezone()
    {
        return $this->_storeConfig->getConfig(
            \Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_TIMEZONE,
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
            return $this->formatDate(
                $date,
                \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM,
                true
            );
        }
        return __('Never');
    }

    /**
     * @return string
     */
    public function getStoreLastLoginDate()
    {
        $date = $this->getCustomerLog()->getLoginAtTimestamp();
        if ($date) {
            $date = $this->_locale->storeDate(
                $this->getCustomer()->getStoreId(),
                $date,
                true
            );
            return $this->formatDate($date, \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM, true);
        }
        return __('Never');
    }

    /**
     * @return string
     */
    public function getStoreLastLoginDateTimezone()
    {
        return $this->_storeConfig->getConfig(
            \Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_TIMEZONE,
            $this->getCustomer()->getStoreId()
        );
    }

    /**
     * @return string
     */
    public function getCurrentStatus()
    {
        $log = $this->getCustomerLog();
        $interval = $this->_modelVisitor->getOnlineMinutesInterval();
        if ($log->getLogoutAt()
            || (strtotime($this->dateTime->now()) - strtotime($log->getLastVisitAt()) > $interval * 60)
        ) {
            return __('Offline');
        }
        return __('Online');
    }

    /**
     * @return string
     */
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

    /**
     * @return null|string
     */
    public function getCreatedInStore()
    {
        return $this->_storeManager->getStore($this->getCustomer()->getStoreId())->getName();
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->getCustomer()->getStoreId();
    }

    public function getBillingAddressHtml()
    {
        $address = $this->getCustomer()->getPrimaryBillingAddress();
        if ($address) {
            return $address->format('html');
        }
        return __('The customer does not have default billing address.');
    }

    /**
     * @return string
     */
    public function getAccordionHtml()
    {
        return $this->getChildHtml('accordion');
    }

    /**
     * @return string
     */
    public function getSalesHtml()
    {
        return $this->getChildHtml('sales');
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Customer View');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return __('Customer View');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->_coreRegistry->registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->_coreRegistry->registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }
}
