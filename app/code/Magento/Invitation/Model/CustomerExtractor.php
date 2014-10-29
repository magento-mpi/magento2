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

class CustomerExtractor extends \Magento\Customer\Model\CustomerExtractor
{
    /**
     * @var \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder,
     */
    protected $customerBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var InvitationProvider
     */
    protected $invitationProvider;

    /**
     * @param Metadata\FormFactory $formFactory
     * @param \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param CustomerGroupServiceInterface $groupService
     * @param \Magento\Framework\Registry $registry
     * @param InvitationProvider $invitationProvider
     */
    public function __construct(
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder,
        \Magento\Framework\StoreManagerInterface $storeManager,
        CustomerGroupServiceInterface $groupService,
        \Magento\Framework\Registry $registry,
        InvitationProvider $invitationProvider
    ) {
        $this->registry = $registry;
        $this->invitationProvider = $invitationProvider;
        parent::__construct($formFactory, $customerBuilder, $storeManager, $groupService);
    }

    /**
     * {@inheritdoc}
     */
    public function extract($formCode, RequestInterface $request)
    {
        $customer = parent::extract($formCode, $request);
        $this->customerBuilder->populate($customer);

        $invitation = $this->invitationProvider->get($request);
        $this->registry->register("skip_confirmation_if_email", $invitation->getEmail());

        $groupId = $invitation->getGroupId();
        if ($groupId) {
            $this->customerBuilder->setGroupId($groupId);
        }

        return $this->customerBuilder->create();
    }
}
