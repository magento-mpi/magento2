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

/**
 * Test class for \Magento\Adminhtml\Block\System\Design\Edit\Tab\General
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_System_Design_Edit_Tab_GeneralTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        Mage::register('design', $objectManager ->create('Magento\Core\Model\Design'));
        $layout = $objectManager ->create('Magento\Core\Model\Layout');
        $block = $layout->addBlock('Magento\Adminhtml\Block\System\Design\Edit\Tab\General');
        $prepareFormMethod = new ReflectionMethod(
            '\Magento\Adminhtml\Block\System\Design\Edit\Tab\General', '_prepareForm'
        );
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('date_from', 'date_to') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}
