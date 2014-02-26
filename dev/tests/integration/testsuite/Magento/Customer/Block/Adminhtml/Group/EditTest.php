<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Adminhtml\Group;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Customer\Service\V1\Data\CustomerGroup;
use Magento\Customer\Service\V1\Data\Filter;
use Magento\Customer\Service\V1\Data\FilterBuilder;
use Magento\Customer\Service\V1\Data\SearchCriteria;
use Magento\Customer\Service\V1\Data\SearchCriteriaBuilder;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Magento\Customer\Block\Adminhtml\Group\Edit
 *
 * @magentoAppArea adminhtml
 */
class EditTest extends AbstractController
{
    /** @var \Magento\View\LayoutInterface */
    private $layout;

    /** @var \Magento\Customer\Service\V1\CustomerGroupService */
    private $customerGroupService;

    /** @var \Magento\Registry */
    private $registry;

    public function setUp()
    {
        parent::setUp();
        $this->layout = Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Layout',
            ['area' => FrontNameResolver::AREA_CODE]
        );
        $this->customerGroupService = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\CustomerGroupService'
        );

        $this->registry = Bootstrap::getObjectManager()->get('Magento\Registry');
    }

    public function tearDown()
    {
        $this->registry->unregister('current_group');
    }

    public function testDeleteButtonNotExistInDefaultGroup()
    {
        $customerGroup = $this->customerGroupService->getDefaultGroup(0);
        $this->registry->register('current_group', $customerGroup);
        $this->getRequest()->setParam('id', $customerGroup->getId());

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
        $searchCriteria = (new SearchCriteriaBuilder())
            ->addFilter((new FilterBuilder())->setField('code')->setValue('custom_group')->create())->create();
        /** @var CustomerGroup $customerGroup */
        $customerGroup = $this->customerGroupService->searchGroups($searchCriteria)->getItems()[0];
        $this->getRequest()->setParam('id', $customerGroup->getId());
        $this->registry->register('current_group', $customerGroup);

        /** @var $block Edit */
        $block = $this->layout->createBlock('Magento\Customer\Block\Adminhtml\Group\Edit', 'block');
        $buttonsHtml = $block->getButtonsHtml();

        $this->assertContains('delete', $buttonsHtml);
    }
}
