<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\Address\Mapper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Adminhtml customer view personal information sales block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PersonalInfo extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    protected $customer;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var \Magento\Customer\Api\Data\CustomerDataBuilder
     */
    protected $customerBuilder;

    /**
     * @var \Magento\Customer\Helper\Address
     */
    protected $addressHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var Mapper
     */
    protected $addressMapper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param AccountManagementInterface $accountManagement
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Registry $registry
     * @param Mapper $addressMapper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        AccountManagementInterface $accountManagement,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder,
        \Magento\Customer\Helper\Address $addressHelper,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Registry $registry,
        Mapper $addressMapper,
        array $data = array()
    ) {
        $this->coreRegistry = $registry;
        $this->accountManagement = $accountManagement;
        $this->groupRepository = $groupRepository;
        $this->customerBuilder = $customerBuilder;
        $this->addressHelper = $addressHelper;
        $this->dateTime = $dateTime;
        $this->addressMapper = $addressMapper;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer()
    {
        if (!$this->customer) {
            $this->customer = $this->customerBuilder->populateWithArray(
                $this->_backendSession->getCustomerData()['account']
            )->create();
        }
        return $this->customer;
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @return string
     */
    public function getStoreCreateDate()
    {
        $date = $this->_localeDate->scopeDate(
            $this->getCustomer()->getStoreId(),
            $this->dateTime->toTimestamp($this->getCustomer()->getCreatedAt()),
            true
        );
        return $this->formatDate($date, TimezoneInterface::FORMAT_TYPE_MEDIUM, true);
    }

    /**
     * @return string
     */
    public function getStoreCreateDateTimezone()
    {
        return $this->_scopeConfig->getValue(
            $this->_localeDate->getDefaultTimezonePath(),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getCustomer()->getStoreId()
        );
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
            TimezoneInterface::FORMAT_TYPE_MEDIUM,
            true
        );
    }

    /**
     * @return string
     */
    public function getIsConfirmedStatus()
    {
        $id = $this->getCustomerId();
        switch ($this->accountManagement->getConfirmationStatus($id)) {
            case AccountManagement::ACCOUNT_CONFIRMED:
                return __('Confirmed');
            case AccountManagement::ACCOUNT_CONFIRMATION_REQUIRED:
                return __('Confirmation Required');
            case AccountManagement::ACCOUNT_CONFIRMATION_NOT_REQUIRED:
                return __('Confirmation Not Required');
        }
        return __('Indeterminate');
    }

    /**
     * @return null|string
     */
    public function getCreatedInStore()
    {
        return $this->_storeManager->getStore(
            $this->getCustomer()->getStoreId()
        )->getName();
    }

    /**
     * @return string
     */
    public function getBillingAddressHtml()
    {
        $result = __('The customer does not have default billing address.');
        try {
            $address = $this->accountManagement->getDefaultBillingAddress($this->getCustomer()->getId());
            if ($address !== null) {
                $result = $this->addressHelper->getFormatTypeRenderer('html')
                    ->renderArray($this->addressMapper->toFlatArray($address));
            }
        } catch (NoSuchEntityException $e) {
            //
        }

        return $result;
    }

    /**
     * @return string|null
     */
    public function getGroupName()
    {
        $customer = $this->getCustomer();
        if ($groupId = $customer->getId() ? $customer->getGroupId() : null) {
            if ($group = $this->getGroup($groupId)) {
                return $group->getCode();
            }
        }

        return null;
    }

    /**
     * @param int $groupId
     * @return \Magento\Customer\Api\Data\GroupInterface|null
     */
    private function getGroup($groupId)
    {
        try {
            $group = $this->groupRepository->getById($groupId);
        } catch (NoSuchEntityException $e) {
            $group = null;
        }
        return $group;
    }
}
