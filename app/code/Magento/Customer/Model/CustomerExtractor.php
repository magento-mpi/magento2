<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model;

use Magento\Customer\Service\V1\CustomerGroupServiceInterface;
use Magento\Framework\App\RequestInterface;

class CustomerExtractor
{
    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Customer\Api\Data\CustomerDataBuilder
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
     * @param Metadata\FormFactory $formFactory
     * @param \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param CustomerGroupServiceInterface $groupService
     */
    public function __construct(
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder,
        \Magento\Framework\StoreManagerInterface $storeManager,
        CustomerGroupServiceInterface $groupService
    ) {
        $this->formFactory = $formFactory;
        $this->customerBuilder = $customerBuilder;
        $this->storeManager = $storeManager;
        $this->groupService = $groupService;
    }

    /**
     * @param string $formCode
     * @param RequestInterface $request
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function extract($formCode, RequestInterface $request)
    {
        $customerForm = $this->formFactory->create('customer', $formCode);

        $allowedAttributes = $customerForm->getAllowedAttributes();
        $isGroupIdEmpty = true;
        $customerData = array();
        foreach ($allowedAttributes as $attribute) {
            // confirmation in request param is the repeated password, not a confirmation code.
            if ($attribute === 'confirmation') {
                continue;
            }
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

        return $this->customerBuilder->create();
    }
}
