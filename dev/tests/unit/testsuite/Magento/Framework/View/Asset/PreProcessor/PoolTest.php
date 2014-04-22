<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\Asset\PreProcessor;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Asset\PreProcessor\Pool
     */
    protected $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = $this->getMock('Magento\ObjectManager');
        $this->factory = new Pool($this->objectManager);
    }

    /**
     * @param string $sourceContentType
     * @param string $targetContentType
     * @param array $expectedResult
     *
     * @dataProvider getPreProcessorsDataProvider
     */
    public function testGetPreProcessors($sourceContentType, $targetContentType, array $expectedResult)
    {
        // Make the object manager to return strings for simplicity of mocking
        $this->objectManager->expects($this->any())
            ->method('get')
            ->with($this->anything())
            ->will($this->returnArgument(0));
        $this->assertSame($expectedResult, $this->factory->getPreProcessors($sourceContentType, $targetContentType));
    }

    public function getPreProcessorsDataProvider()
    {
        return array(
            'css => css' => array(
                'css', 'css',
                array('Magento\Framework\View\Asset\PreProcessor\ModuleNotation'),
            ),
            'css => less (irrelevant)' => array(
                'css', 'less',
                array(),
            ),
            'less => css' => array(
                'less', 'css',
                array(
                    'Magento\Css\PreProcessor\Less',
                    'Magento\Framework\View\Asset\PreProcessor\ModuleNotation',
                ),
            ),
            'less => less' => array(
                'less', 'less',
                array(
                    'Magento\Less\PreProcessor\Instruction\MagentoImport',
                    'Magento\Less\PreProcessor\Instruction\Import',
                ),
            ),
            'txt => log (unsupported)' => array(
                'txt', 'log',
                array(),
            ),
        );
    }
}
