<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Model;

use Magento\Customer\Model\Metadata;
use Magento\Customer\Service\V1\CustomerGroupServiceInterface;
use Magento\Framework\App\RequestInterface;

class CustomerExtractor
{
    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerBuilder
     */
    protected $customerBuilder;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerGroupServiceInterface
     */
    protected $groupService;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var InvitationProvider
     */
    protected $invitationProvider;

    /**
     * @param \Magento\Customer\Model\Metadata\FormFactory $formFactory
     * @param \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param CustomerGroupServiceInterface $groupService
     * @param \Magento\Framework\Registry $registry
     * @param InvitationProvider $invitationProvider
     */
    public function __construct(
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder,
        \Magento\Framework\StoreManagerInterface $storeManager,
        CustomerGroupServiceInterface $groupService,
        \Magento\Framework\Registry $registry,
        InvitationProvider $invitationProvider
    ) {
        $this->formFactory = $formFactory;
        $this->customerBuilder = $customerBuilder;
        $this->storeManager = $storeManager;
        $this->groupService = $groupService;
        $this->registry = $registry;
        $this->invitationProvider = $invitationProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($formCode, RequestInterface $request)
    {
        $customerForm = $this->formFactory->create('customer', $formCode);

        $allowedAttributes = $customerForm->getAllowedAttributes();
        $isGroupIdEmpty = true;
        $customerData = array();
        foreach ($allowedAttributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if ($attributeCode == 'group_id') {
                $isGroupIdEmpty = false;
            }
            $customerData[$attributeCode] = $request->getParam($attributeCode);
        }
        $this->customerBuilder->populateWithArray($customerData);
        $store = $this->storeManager->getStore();
        if ($isGroupIdEmpty) {
            $this->customerBuilder->setGroupId($this->groupService->getDefaultGroup($store->getId())->getId());
        }

        $this->customerBuilder->setWebsiteId($store->getWebsiteId());
        $this->customerBuilder->setStoreId($store->getId());

        $invitation = $this->invitationProvider->get($request);
        $this->registry->register("skip_confirmation_if_email", $invitation->getEmail());

        $groupId = $invitation->getGroupId();
        if ($groupId) {
            $this->customerBuilder->setGroupId($groupId);
        }

        return $this->customerBuilder->create();
    }
}
