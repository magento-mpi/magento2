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

namespace Magento\Adminhtml\Block\Widget\Form;

/**
 * @magentoAppArea adminhtml
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFormHtml()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\Layout');
        // Create block with blocking _prepateLayout(), which is used by block to instantly add 'form' child
        /** @var $block \Magento\Adminhtml\Block\Widget\Form\Container */
        $block = $this->getMock('Magento\Adminhtml\Block\Widget\Form\Container', array('_prepareLayout'),
            array(
                $objectManager->create('Magento\Core\Helper\Data'),
                $objectManager->create('Magento\Backend\Block\Template\Context'),
            )
        );

        $layout->addBlock($block, 'block');
        $form = $layout->addBlock('Magento\Core\Block\Text', 'form', 'block');

        $expectedHtml = '<b>html</b>';
        $this->assertNotEquals($expectedHtml, $block->getFormHtml());
        $form->setText($expectedHtml);
        $this->assertEquals($expectedHtml, $block->getFormHtml());
    }
}
