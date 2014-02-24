<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Adminhtml\Group\Edit;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Customer\Service\V1\Data\CustomerGroup;
use Magento\Customer\Service\V1\Data\FilterBuilder;
use Magento\Customer\Service\V1\Data\SearchCriteriaBuilder;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Magento\Customer\Block\Adminhtml\Group\Edit\Form
 *
 * @magentoAppArea adminhtml
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\View\LayoutInterface */
    private $layout;

    /** @var \Magento\Customer\Service\V1\CustomerGroupService */
    private $customerGroupService;

    /** @var \Magento\Core\Model\Registry */
    private $registry;

    public function setUp()
    {
        parent::setUp();
        $this->layout = Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Layout',
            ['area' => FrontNameResolver::AREA_CODE]
        );
        $this->customerGroupService = Bootstrap::getObjectManager()->get(
            'Magento\Customer\Service\V1\CustomerGroupServiceInterface'
        );

        $this->registry = Bootstrap::getObjectManager()->get('Magento\Core\Model\Registry');
    }

    public function tearDown()
    {
        $this->registry->unregister('current_group');
    }

    public function testGetForm()
    {
        $customerGroup = $this->customerGroupService->getDefaultGroup(0);
        $this->registry->register('current_group', $customerGroup);

        /** @var $block Form */
        $block = $this->layout->createBlock('Magento\Customer\Block\Adminhtml\Group\Edit\Form', 'block');
        $form = $block->getForm();

        $this->assertEquals('edit_form', $form->getId());
        $baseFieldSet = $form->getElement('base_fieldset');
        $this->assertNotNull($baseFieldSet);
        $groupCodeElement = $form->getElement('customer_group_code');
        $this->assertNotNull($groupCodeElement);
        $taxClassIdElement = $form->getElement('tax_class_id');
        $this->assertNotNull($taxClassIdElement);
        $idElement = $form->getElement('id');
        $this->assertNotNull($idElement);
        $this->assertEquals('1', $idElement->getValue());
        $this->assertEquals('3', $taxClassIdElement->getValue());
        $this->assertEquals('General', $groupCodeElement->getValue());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testGetFormExistInCustomGroup()
    {
        $searchCriteria = (new SearchCriteriaBuilder())
            ->addFilter((new FilterBuilder())->setField('code')->setValue('custom_group')->create())->create();
        /** @var CustomerGroup $customerGroup */
        $customerGroup = $this->customerGroupService->searchGroups($searchCriteria)->getItems()[0];
        $this->registry->register('current_group', $customerGroup);

        /** @var $block Form */
        $block = $this->layout->createBlock('Magento\Customer\Block\Adminhtml\Group\Edit\Form', 'block');
        $form = $block->getForm();

        $this->assertEquals('edit_form', $form->getId());
        $baseFieldSet = $form->getElement('base_fieldset');
        $this->assertNotNull($baseFieldSet);
        $groupCodeElement = $form->getElement('customer_group_code');
        $this->assertNotNull($groupCodeElement);
        $taxClassIdElement = $form->getElement('tax_class_id');
        $this->assertNotNull($taxClassIdElement);
        $idElement = $form->getElement('id');
        $this->assertNotNull($idElement);
        $this->assertEquals($customerGroup->getId(), $idElement->getValue());
        $this->assertEquals($customerGroup->getTaxClassId(), $taxClassIdElement->getValue());
        $this->assertEquals($customerGroup->getCode(), $groupCodeElement->getValue());
    }
}
