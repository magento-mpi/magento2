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

namespace Magento\Backend\Block\System\Design\Edit\Tab;

/**
 * Test class for \Magento\Backend\Block\System\Design\Edit\Tab\General
 * @magentoAppArea adminhtml
 */
class GeneralTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\View\DesignInterface')
            ->setArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
            ->setDefaultDesignTheme();
        $objectManager->get('Magento\Registry')
            ->register('design', $objectManager ->create('Magento\Core\Model\Design'));
        $layout = $objectManager ->create('Magento\Core\Model\Layout');
        $block = $layout->addBlock('Magento\Backend\Block\System\Design\Edit\Tab\General');
        $prepareFormMethod = new \ReflectionMethod(
            'Magento\Backend\Block\System\Design\Edit\Tab\General', '_prepareForm'
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
