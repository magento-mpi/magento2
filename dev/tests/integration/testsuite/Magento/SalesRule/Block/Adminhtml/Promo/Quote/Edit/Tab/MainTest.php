<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab;

/**
 * Test class for \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Main
 *
 * @magentoAppArea adminhtml
 */
class MainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get(
            'Magento\View\DesignInterface'
        )->setArea(
            \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
        )->setDefaultDesignTheme();
        $objectManager->get(
            'Magento\Registry'
        )->register(
            'current_promo_quote_rule',
            $objectManager->create('Magento\SalesRule\Model\Rule')
        );

        $layout = $objectManager->create('Magento\View\Layout');
        $block = $layout->addBlock('Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Main');
        $prepareFormMethod = new \ReflectionMethod(
            'Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Main',
            '_prepareForm'
        );
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('from_date', 'to_date') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }

        // assert Customer Groups field
        $customerGroupsField = $form->getElement('customer_group_ids');
        $customerGroupService = $objectManager->create('Magento\Customer\Service\V1\CustomerGroupServiceInterface');
        $objectConverter = $objectManager->get('Magento\Convert\Object');
        $groups = $customerGroupService->getGroups();
        $expected = $objectConverter->toOptionArray($groups, 'id', 'code');
        $this->assertEquals($expected, $customerGroupsField->getValues());
    }
}
