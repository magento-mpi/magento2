<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\StoreManagerInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Api\Data\GroupDataBuilder;
use Magento\Customer\Model\GroupFactory;

class GroupManagement implements \Magento\Customer\Api\GroupManagementInterface
{
    const XML_PATH_DEFAULT_ID = 'customer/create_account/default_group';

    const NOT_LOGGED_IN_ID = 0;

    const CUST_GROUP_ALL = 32000;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var GroupFactory
     */
    protected $groupFactory;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var GroupDataBuilder
     */
    protected $groupBuilder;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param GroupFactory $groupFactory
     * @param GroupRepositoryInterface $groupRepository
     * @param GroupDataBuilder $groupBuilder
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        GroupFactory $groupFactory,
        GroupRepositoryInterface $groupRepository,
        GroupDataBuilder $groupBuilder
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->groupFactory = $groupFactory;
        $this->groupRepository = $groupRepository;
        $this->groupBuilder = $groupBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadonly($groupId)
    {
        /** @var \Magento\Customer\Model\Group $group */
        $group = $this->groupFactory->create();
        $group->load($groupId);
        if (is_null($group->getId())) {
            throw NoSuchEntityException::singleField('groupId', $groupId);
        }
        return $groupId > 0 && !$group->usesAsDefault();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultGroup($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->storeManager->getStore()->getCode();
        }
        try {
            $groupId = $this->scopeConfig->getValue(
                self::XML_PATH_DEFAULT_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        } catch (\Magento\Framework\App\InitException $e) {
            throw NoSuchEntityException::singleField('storeId', $storeId);
        }
        try {
            return $this->groupRepository->get($groupId);
        } catch (NoSuchEntityException $e) {
            throw NoSuchEntityException::doubleField('groupId', $groupId, 'storeId', $storeId);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNotLoggedInGroup($storeId = null)
    {
        return $this->groupBuilder->setId(self::NOT_LOGGED_IN_ID)
            ->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllGroup($storeId = null)
    {
        return $this->groupBuilder->setId(self::CUST_GROUP_ALL)
            ->create();
    }
}
