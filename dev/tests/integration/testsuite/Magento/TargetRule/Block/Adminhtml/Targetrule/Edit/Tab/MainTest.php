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
        $objectManager->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $objectManager->get('Magento\Core\Model\Registry')
            ->register('current_target_rule', \Mage::getModel('Magento\TargetRule\Model\Rule'));

        $block = \Mage::app()->getLayout()->createBlock(
            'Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab\Main'
        );
        $prepareFormMethod = new \ReflectionMethod(
            'Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab\Main', '_prepareForm');
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
