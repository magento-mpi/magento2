<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Adminhtml\Group;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Service\V1\Data\CustomerGroup;
use Magento\Framework\Service\V1\Data\FilterBuilder;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Magento\Customer\Block\Adminhtml\Group\Edit
 *
 * @magentoAppArea adminhtml
 */
class EditTest extends AbstractController
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupService
     */
    private $customerGroupService;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        parent::setUp();
        $this->layout = Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );
        $this->customerGroupService = Bootstrap::getObjectManager()
            ->create('Magento\Customer\Service\V1\CustomerGroupService');
        $this->registry = Bootstrap::getObjectManager()->get('Magento\Framework\Registry');
    }

    /**
     * Execute per test cleanup.
     */
    public function tearDown()
    {
        $this->registry->unregister(RegistryConstants::CURRENT_GROUP_ID);
    }

    /**
     * Verify that the Delete button does not exist for the default group.
     * @magentoAppIsolation enabled
     */
    public function testDeleteButtonNotExistInDefaultGroup()
    {
        $groupId = $this->customerGroupService->getDefaultGroup(0)->getId();
        $this->registry->register(RegistryConstants::CURRENT_GROUP_ID, $groupId);
        $this->getRequest()->setParam('id', $groupId);

        /** @var $block Edit */
        $block = $this->layout->createBlock('Magento\Customer\Block\Adminhtml\Group\Edit', 'block');
        $buttonsHtml = $block->getButtonsHtml();

        $this->assertNotContains('delete', $buttonsHtml);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testDeleteButtonExistInCustomGroup()
    {
        $builder = Bootstrap::getObjectManager()->create('\Magento\Framework\Service\V1\Data\FilterBuilder');
        /** @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteria */
        $searchCriteria = Bootstrap::getObjectManager()
            ->create('Magento\Framework\Service\V1\Data\SearchCriteriaBuilder')
            ->addFilter([$builder->setField('code')->setValue('custom_group')->create()])->create();
        /** @var CustomerGroup $customerGroup */
        $customerGroup = $this->customerGroupService->searchGroups($searchCriteria)->getItems()[0];
        $this->getRequest()->setParam('id', $customerGroup->getId());
        $this->registry->register(RegistryConstants::CURRENT_GROUP_ID, $customerGroup->getId());

        /** @var $block Edit */
        $block = $this->layout->createBlock('Magento\Customer\Block\Adminhtml\Group\Edit', 'block');
        $buttonsHtml = $block->getButtonsHtml();

        $this->assertContains('delete', $buttonsHtml);
    }
}
