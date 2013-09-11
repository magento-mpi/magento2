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

/**
 * Test class for \Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab\Main
 *
 * @magentoAppArea adminhtml
 */
class Magento_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_MainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        Mage::register('current_target_rule', Mage::getModel('Magento\TargetRule\Model\Rule'));

        $block = Mage::app()->getLayout()->createBlock(
            '\Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab\Main'
        );
        $prepareFormMethod = new ReflectionMethod(
            '\Magento\TargetRule\Block\Adminhtml\Targetrule\Edit\Tab\Main', '_prepareForm');
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
