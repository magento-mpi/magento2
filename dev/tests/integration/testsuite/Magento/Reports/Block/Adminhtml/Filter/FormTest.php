<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Adminhtml\Filter;

/**
 * Test class for \Magento\Reports\Block\Adminhtml\Filter\Form
 * @magentoAppArea adminhtml
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\DesignInterface'
        )->setArea(
            \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
        )->setDefaultDesignTheme();
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Framework\View\Layout');
        $block = $layout->addBlock('Magento\Reports\Block\Adminhtml\Filter\Form');
        $prepareFormMethod = new \ReflectionMethod('Magento\Reports\Block\Adminhtml\Filter\Form', '_prepareForm');
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('from', 'to') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}
