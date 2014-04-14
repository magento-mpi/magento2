<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab;

/**
 * @magentoAppArea adminhtml
 */
class MainTest extends \PHPUnit_Framework_TestCase
{
    public function testPackageThemeElement()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Registry')->register('current_widget_instance', new \Magento\Object());
        /** @var \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main $block */
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\LayoutInterface'
        )->createBlock(
            'Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main'
        );
        $block->setTemplate(null);
        $block->toHtml();
        $element = $block->getForm()->getElement('theme_id');
        $this->assertInstanceOf('Magento\Framework\Data\Form\Element\Select', $element);
        $this->assertTrue($element->getDisabled());
    }

    public function testTypeElement()
    {
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\Layout'
        )->createBlock(
            'Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab\Main'
        );
        $block->setTemplate(null);
        $block->toHtml();
        $element = $block->getForm()->getElement('instance_code');
        $this->assertInstanceOf('Magento\Framework\Data\Form\Element\Select', $element);
        $this->assertTrue($element->getDisabled());
    }
}
