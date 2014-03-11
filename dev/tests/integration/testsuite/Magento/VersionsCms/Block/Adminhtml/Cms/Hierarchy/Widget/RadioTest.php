<?php
/**
 * {license_notice}
 *
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Widget;

use \Magento\TestFramework\Helper\Bootstrap;
use \Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Widget\Radio;
use \Magento\View\LayoutInterface;

/**
 * @magentoAppArea adminhtml
 */
class RadioTest extends \PHPUnit_Framework_TestCase
{
    /** @var LayoutInterface */
    protected $layoutMock;

    /** @var Radio */
    protected $block;

    public function setUp()
    {
        parent::setUp();
        $this->layoutMock = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $this->block = Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('\Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Widget\Radio');
    }

    /**
     * @dataProvider getParametersDataProvider
     * @magentoAppIsolation enabled
     * @param array|null $blockOptions
     * @param array|null $widgetOptions
     * @param array $expectedResult
     */
    public function testGetParameters($blockOptions, $widgetOptions, $expectedResult)
    {
        $this->block->setWidgetValues($blockOptions);
        $this->layoutMock->expects($this->any())
            ->method('getBlock')
            ->with('wysiwyg_widget.options')
            ->will($this->returnValue($blockOptions ? $this->block : null));

        if ($widgetOptions) {
            $widgetInstance = $this->getMock(
                'Magento\Widget\Model\Widget\Instance', array('getWidgetParameters'), array(), '', false
            );
            $widgetInstance->expects($this->once())
                ->method('getWidgetParameters')
                ->will($this->returnValue($widgetOptions));

            /** @var $objectManager \Magento\TestFramework\ObjectManager */
            $objectManager = Bootstrap::getObjectManager();

            $objectManager->get('Magento\Registry')->unregister('current_widget_instance');
            $objectManager->get('Magento\Registry')->register('current_widget_instance', $widgetInstance);
        }

        $this->block->setLayout($this->layoutMock);
        $this->block->getParameters();
        $result = $this->block->getParameters();
        $this->assertEquals($result, $expectedResult);
    }

    /**
     * @see self::testGetParameters()
     * @return array
     */
    public function getParametersDataProvider()
    {
        return array(
            array(array('key' => 'value'), null, array('key' => 'value')),
            array(null, array('key' => 'value'), array('key' => 'value')),
            array(null, null, array()),
        );
    }
}
