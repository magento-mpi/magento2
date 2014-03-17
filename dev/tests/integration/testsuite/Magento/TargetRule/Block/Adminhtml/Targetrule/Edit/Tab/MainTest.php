<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab;

/**
 * Test class for \Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab\Main
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
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get(
            'Magento\View\DesignInterface'
        )->setArea(
            \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
        )->setDefaultDesignTheme();
        $objectManager->get(
            'Magento\Registry'
        )->register(
            'current_target_rule',
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\TargetRule\Model\Rule')
        );

        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\LayoutInterface'
        )->createBlock(
            'Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab\Main'
        );
        $prepareFormMethod = new \ReflectionMethod(
            'Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab\Main',
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
    }
}
