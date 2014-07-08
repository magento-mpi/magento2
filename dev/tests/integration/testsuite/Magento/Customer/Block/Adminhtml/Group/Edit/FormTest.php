<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Adminhtml\Group\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Service\V1\Data\CustomerGroup;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Tax\Service\V1\Data\TaxClass;

/**
 * Magento\Customer\Block\Adminhtml\Group\Edit\Form
 *
 * @magentoAppArea adminhtml
 */
class FormTest extends \PHPUnit_Framework_TestCase
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
        $this->layout = Bootstrap::getObjectManager()->create(
            'Magento\Framework\View\Layout'
        );
        $this->customerGroupService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');
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
     * Test retrieving a valid group form.
     */
    public function testGetForm()
    {
        $this->registry
            ->register(RegistryConstants::CURRENT_GROUP_ID, $this->customerGroupService->getDefaultGroup(0)->getId());

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
        $this->assertEquals($this->getAllTaxClassOptions(), $taxClassIdElement->getData('values'));
        $this->assertEquals('General', $groupCodeElement->getValue());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testGetFormExistInCustomGroup()
    {
        $builder = Bootstrap::getObjectManager()->create('\Magento\Framework\Service\V1\Data\FilterBuilder');
        /** @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteria */
        $searchCriteria = Bootstrap::getObjectManager()
            ->create('Magento\Framework\Service\V1\Data\SearchCriteriaBuilder')
            ->addFilter([$builder->setField('code')->setValue('custom_group')->create()])->create();
        /** @var CustomerGroup $customerGroup */
        $customerGroup = $this->customerGroupService->searchGroups($searchCriteria)->getItems()[0];
        $this->registry->register(RegistryConstants::CURRENT_GROUP_ID, $customerGroup->getId());

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
        $this->assertEquals($this->getAllTaxClassOptions(), $taxClassIdElement->getData('values'));
        $this->assertEquals($customerGroup->getCode(), $groupCodeElement->getValue());
    }

    /**
     * @return array
     */
    protected function getAllTaxClassOptions()
    {
        $filters[] = Bootstrap::getObjectManager()->create('\Magento\Framework\Service\V1\Data\FilterBuilder')
            ->setField('class_type')
            ->setValue(TaxClass::TYPE_CUSTOMER)
            ->create();
        /** @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteria */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create('\Magento\Framework\Service\V1\Data\SearchCriteriaBuilder');
        $searchCriteriaBuilder->addFilter($filters);
        return Bootstrap::getObjectManager()->create('\Magento\Framework\Convert\Object')->toOptionArray(
            Bootstrap::getObjectManager()->create('\Magento\Tax\Service\V1\TaxClassServiceInterface')
                ->searchTaxClass($searchCriteriaBuilder->create())
                ->getItems(),
            'classid',
            'classname'
        );
    }
}
