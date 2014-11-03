<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Service\V1\Data\AddressConverter;
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
     * @var CustomerAccountServiceInterface
     */
    protected $accountService;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface
     */
    protected $addressService;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $groupService;

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
     * @param \Magento\Backend\Block\Template\Context $context
     * @param CustomerAccountServiceInterface $accountService
     * @param \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService
     * @param \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        CustomerAccountServiceInterface $accountService,
        \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService,
        \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder,
        \Magento\Customer\Helper\Address $addressHelper,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->coreRegistry = $registry;
        $this->accountService = $accountService;
        $this->addressService = $addressService;
        $this->groupService = $groupService;
        $this->customerBuilder = $customerBuilder;
        $this->addressHelper = $addressHelper;
        $this->dateTime = $dateTime;
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
        switch ($this->accountService->getConfirmationStatus($id)) {
            case CustomerAccountServiceInterface::ACCOUNT_CONFIRMED:
                return __('Confirmed');
            case CustomerAccountServiceInterface::ACCOUNT_CONFIRMATION_REQUIRED:
                return __('Confirmation Required');
            case CustomerAccountServiceInterface::ACCOUNT_CONFIRMATION_NOT_REQUIRED:
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
     * @return string|null
     */
    public function getBillingAddressHtml()
    {
        try {
            $address = $this->addressService->getAddress($this->getCustomer()->getDefaultBilling());
        } catch (NoSuchEntityException $e) {
            return __('The customer does not have default billing address.');
        }
        return $this->addressHelper->getFormatTypeRenderer(
            'html'
        )->renderArray(
            AddressConverter::toFlatArray($address)
        );
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
     * @return \Magento\Customer\Service\V1\Data\CustomerGroup|null
     */
    private function getGroup($groupId)
    {
        try {
            $group = $this->groupService->getGroup($groupId);
        } catch (NoSuchEntityException $e) {
            $group = null;
        }
        return $group;
    }
}
