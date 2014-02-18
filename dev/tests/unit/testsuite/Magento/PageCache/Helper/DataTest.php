<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\PageCache\Helper\Data
 */
namespace Magento\PageCache\Helper;

/**
 * Class DataTest
 *
 * @package Magento\PageCache\Controller
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\PageCache\Helper\Data */
    protected $helper;

    /** @var \Magento\View\Layout\ProcessorInterface */
    protected $updateLayoutMock;

    /** @var \Magento\Theme\Model\Layout\Config */
    protected $configMock;

    public function testMaxAgeCache()
    {
        // one year
        $age = 365 * 24 * 60 * 60;
        $this->assertEquals($age, \Magento\PageCache\Helper\Data::PRIVATE_MAX_AGE_CACHE);
    }

    /**
     *
     */
    public function testGetActualHandles()
    {
        $this->prepareMocks();
        $layoutHandles = [
            'handle1',
            'config_layout_handle1',
            'handle2'
        ];
        $configHandles = [
            'config_layout_handle1'
        ];
        $resultHandles = [
            'default',
            'config_layout_handle1'
        ];

        $this->updateLayoutMock->expects($this->once())
            ->method('getHandles')
            ->will($this->returnValue($layoutHandles));
        $this->configMock->expects($this->once())
            ->method('getPageLayoutHandles')
            ->will($this->returnValue($configHandles));

        $this->assertEquals($resultHandles, $this->helper->getActualHandles());
    }

    protected function prepareMocks()
    {
        $this->configMock = $this->getMock('Magento\Theme\Model\Layout\Config', [], [], '', false);
        $viewMock = $this->getMock('Magento\App\View', ['getLayout'], ['getPageLayoutHandles'], '', false);
        $layoutMock = $this->getMockForAbstractClass(
            'Magento\View\LayoutInterface',
            [],
            '',
            false,
            true,
            true,
            ['getUpdate']
        );
        $this->updateLayoutMock = $this->getMockForAbstractClass(
            'Magento\View\Layout\ProcessorInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );

        $viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));
        $layoutMock->expects($this->once())
            ->method('getUpdate')
            ->will($this->returnValue($this->updateLayoutMock));

        $this->helper = new \Magento\PageCache\Helper\Data($this->configMock, $viewMock);
    }
}
