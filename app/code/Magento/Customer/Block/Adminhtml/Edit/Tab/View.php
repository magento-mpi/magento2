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

use \Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Controller\Adminhtml\Index;
use Magento\Exception\NoSuchEntityException;

/**
 * Customer account form block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View
    extends \Magento\Backend\Block\Template
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Customer\Service\V1\Dto\Customer
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
     * @var \Magento\Customer\Service\V1\CustomerServiceInterface
     */
    protected $_customerService;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_accountService;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface
     */
    protected $_addressService;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $_groupService;

    /**
     * @var \Magento\Customer\Helper\Address
     */
    protected $_addressHelper;

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
     * @param \Magento\Customer\Service\V1\CustomerServiceInterface $customerService
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $accountService
     * @param \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param \Magento\Log\Model\CustomerFactory $logFactory
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Log\Model\Visitor $modelVisitor
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Service\V1\CustomerServiceInterface $customerService,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $accountService,
        \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService,
        \Magento\Customer\Helper\Address $addressHelper,
        \Magento\Log\Model\CustomerFactory $logFactory,
        \Magento\Core\Model\Registry $registry,
        \Magento\Log\Model\Visitor $modelVisitor,
        \Magento\Stdlib\DateTime $dateTime,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_modelVisitor = $modelVisitor;
        $this->_customerService = $customerService;
        $this->_accountService = $accountService;
        $this->_addressService = $addressService;
        $this->_groupService = $groupService;
        $this->_addressHelper = $addressHelper;
        $this->_logFactory = $logFactory;
        $this->dateTime = $dateTime;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Customer\Service\V1\Dto\Customer
     */
    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = $this->_customerService->getCustomer(
                $this->_coreRegistry->registry(Index::REGISTRY_CURRENT_CUSTOMER_ID)
            );
        }
        return $this->_customer;
    }

    /**
     * @param int $groupId
     * @return \Magento\Customer\Service\V1\Dto\CustomerGroup|null
     */
    private function getGroup($groupId)
    {
        try {
            $group = $this->_groupService->getGroup($groupId);
        } catch (\Magento\Exception\NoSuchEntityException $e) {
            $group = null;
        }
        return $group;
    }

    /**
     * @return string|null
     */
    public function getGroupName()
    {
        $customer = $this->getCustomer();

        if ($groupId = ($customer->getCustomerId() ? $customer->getGroupId() : null)) {
            if ($group = $this->getGroup($groupId)) {
                return $group->getCode();
            }
        }

        return null;
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
                ->loadByCustomer($this->getCustomer()->getCustomerId());
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
            $this->getCustomer()->getCreatedAt(),
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
        $id = $this->getCustomer()->getCustomerId();
        switch($this->_accountService->getConfirmationStatus($id)) {
            case CustomerAccountServiceInterface::ACCOUNT_CONFIRMED:
                return __('Confirmed');
            case CustomerAccountServiceInterface::ACCOUNT_CONFIRMATION_REQUIRED:
                return __('Confirmation Required');
            case CustomerAccountServiceInterface::ACCOUNT_CONFIRMATION_NOT_REQUIRED:
                return __('Confirmation Not Required');
        }
    }

    /**
     * @return null|string
     */
    public function getCreatedInStore()
    {
        return $this->_storeManager->getStore($this->getStoreId())->getName();
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
        try {
            $address = $this->_addressService->getAddressById($this->getCustomer()->getDefaultBilling());
        } catch (NoSuchEntityException $e) {
            return __('The customer does not have default billing address.');
        }
        return $this->_addressHelper->getFormatTypeRenderer('html')->renderArray(
            $address->getAttributes()
        );
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
        if ($this->getCustomer()->getCustomerId()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomer()->getCustomerId()) {
            return false;
        }
        return true;
    }
}
