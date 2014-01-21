<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Adminhtml\Group;
use Magento\Customer\Service\V1\Dto\CustomerGroup;
use Magento\Customer\Service\V1\Dto\Filter;
use Magento\Customer\Service\V1\Dto\SearchCriteria;

/**
 * Magento\Customer\Block\Adminhtml\Grid
 *
 * @magentoAppArea adminhtml
 */
class EditTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /** @var \Magento\View\LayoutInterface */
    private $layout;

    /** @var \Magento\Customer\Service\V1\CustomerGroupService */
    private $customerGroupService;

    public function setUp()
    {
        parent::setUp();
        $this->layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Layout',
            array('area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
        );
        $this->customerGroupService = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\CustomerGroupService'
        );
    }

    public function tearDown()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Registry')
            ->unregister('current_group');
    }

    public function testDeleteButtonNotExistInDefaultGroup()
    {
        $customerGroup = $this->customerGroupService->getDefaultGroup(0);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Registry')
            ->register('current_group', $customerGroup);
        $this->getRequest()->setParam('id', $customerGroup->getId());

        /** @var $block \Magento\Customer\Block\Adminhtml\Group\Edit */
        $block = $this->layout->createBlock('Magento\Customer\Block\Adminhtml\Group\Edit', 'block');
        $buttonsHtml = $block->getButtonsHtml();

        $this->assertNotContains('delete', $buttonsHtml);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testDeleteButtonExistInCustomGroup()
    {
        $searchCriteria = new SearchCriteria([
            'filters' => [new Filter([
                'field'             => 'customer_group_code',
                'value'             => 'custom_group',
                'condition_type'    => 'or',
            ])]
        ]);
        /** @var CustomerGroup $customerGroup */
        $customerGroup = $this->customerGroupService->searchGroups($searchCriteria)->getItems()[0];
        $this->getRequest()->setParam('id', $customerGroup->getId());
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Registry')
            ->register('current_group', $customerGroup);

        /** @var $block \Magento\Customer\Block\Adminhtml\Group\Edit */
        $block = $this->layout->createBlock('Magento\Customer\Block\Adminhtml\Group\Edit', 'block');
        $buttonsHtml = $block->getButtonsHtml();

        $this->assertContains('delete', $buttonsHtml);
    }
}
