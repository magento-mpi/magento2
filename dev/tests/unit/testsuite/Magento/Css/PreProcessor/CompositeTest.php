<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class CompositeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Css\PreProcessor\Composite */
    protected $composite;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\View\Asset\PreProcessorFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $preProcessorFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $callMap = [];

    protected function setUp()
    {
        $this->preProcessorFactoryMock = $this->getMock('Magento\View\Asset\PreProcessorFactory', [], [], '', false);
        $this->objectManagerHelper = new ObjectManagerHelper($this);
    }

    /**
     * @param array $preProcessors
     * @param array $createMap
     * @dataProvider processDataProvider
     */
    public function testProcess($preProcessors, $createMap)
    {
        $publisherFile = $this->getMock('Magento\View\Publisher\CssFile', [], [], '', false);
        $targetDir = $this->getMock('Magento\Filesystem\Directory\WriteInterface', array(), array(), '', false);

        foreach ($createMap as $className) {
            $this->callMap[$className] = $this->getMock($className, array(), array(), '', false);
            $this->callMap[$className]->expects($this->once())
                ->method('process')
                ->with(
                    $this->equalTo($publisherFile),
                    $this->equalTo($targetDir)
                )
                ->will($this->returnValue($publisherFile));
        }

        $this->preProcessorFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(array($this, 'createProcessor')));

        $this->composite = $this->objectManagerHelper->getObject(
            'Magento\Css\PreProcessor\Composite',
            [
                'preProcessorFactory' => $this->preProcessorFactoryMock,
                'preProcessors' => $preProcessors
            ]
        );

        $this->assertEquals($publisherFile, $this->composite->process($publisherFile, $targetDir));
    }

    /**
     * Create pre-processor callback
     *
     * @param string $className
     * @return \Magento\View\Asset\PreProcessor\PreProcessorInterface[]
     */
    public function createProcessor($className)
    {
        return $this->callMap[$className];
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            'one processor - LESS' => [
                'preProcessors' => [
                    'css_source_processor' => 'Magento\Css\PreProcessor\Less',
                ],
                'createMap' => [
                    'Magento\Css\PreProcessor\Less',
                ],
            ],
            'list of pre-processors' => [
                'preProcessors' => [
                    'css_source_processor' => 'Magento\Css\PreProcessor\Less',
                    'css_url_processor' => 'Magento\Css\PreProcessor\UrlResolver',
                ],
                'createMap' => [
                    'Magento\Css\PreProcessor\Less',
                    'Magento\Css\PreProcessor\UrlResolver',
                ],
            ],
            'no processors' => [
                'preProcessors' => [],
                'createMap' => [],
            ],
        ];
    }
}
