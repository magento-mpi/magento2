<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Block\Adminhtml\Invitation\Add;

/**
 * Test class for \Magento\Invitation\Block\Adminhtml\Invitation\Add\Form
 *
 * @magentoAppArea adminhtml
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareFormForCustomerGroup()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get(
            'Magento\View\DesignInterface'
        )->setArea(
            \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
        )->setDefaultDesignTheme();

        $block = $objectManager->create('Magento\Invitation\Block\Adminhtml\Invitation\Add\Form');
        $block->setLayout($objectManager->create('Magento\View\Layout'));
        $prepareFormMethod = new \ReflectionMethod(
            'Magento\Invitation\Block\Adminhtml\Invitation\Add\Form',
            '_prepareForm'
        );
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();

        $element = $form->getElement('group_id');
        $this->assertContains("General", $element->getValues());
        $this->assertContains("Wholesale", $element->getValues());
        $this->assertContains("Retailer", $element->getValues());
    }
}
