<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\PreProcessor;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class CompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\PreProcessor\Composite
     */
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
     * @param array $extension
     * @param array $preProcessorsConfig
     * @param array $createMap
     * @param string $expectedResult
     * @dataProvider processDataProvider
     */
    public function testProcess($extension, $preProcessorsConfig, $createMap, $expectedResult)
    {
        $this->composite = $this->objectManagerHelper->getObject(
            'Magento\View\Asset\PreProcessor\Composite',
            [
                'preProcessorFactory' => $this->preProcessorFactoryMock,
                'preProcessorsConfig' => $preProcessorsConfig
            ]
        );

        $publisherFile = $this->getMock('Magento\View\Publisher\CssFile', [], [], '', false);
        $publisherFile->expects($this->once())
            ->method('getExtension')
            ->will($this->returnValue($extension));

        $targetDir = $this->getMock('Magento\Filesystem\Directory\WriteInterface', array(), array(), '', false);

        $willSourcePathChanged = false;
        foreach ($createMap as $className => $isExpected) {
            if (!$willSourcePathChanged) {
                $willSourcePathChanged = $isExpected === 'expected';
            }
            $this->callMap[$className] = $this->getMock($className, array('process'), array(), '', false);

            if ($isExpected === 'expected') {
                $this->callMap[$className]->expects($this->once())
                    ->method('process')
                    ->with(
                        $this->equalTo($publisherFile),
                        $this->equalTo($targetDir)
                    )
                    ->will($this->returnValue($expectedResult));
            } else {
                $this->callMap[$className]->expects($this->never())->method('process');
            }
        }

        if ($willSourcePathChanged) {
            $publisherFile->expects($this->atLeastOnce())
                ->method('setSourcePath')
                ->with($this->equalTo($expectedResult));
        }

        $publisherFile->expects($this->once())
            ->method('getSourcePath')
            ->will($this->returnValue($expectedResult));

        $this->preProcessorFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(array($this, 'createProcessor')));

        $result = $this->composite->process($publisherFile, $targetDir);
        $this->assertEquals($expectedResult, $result);
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
            'list of processors for css' => [
                'extension' => 'css',
                'preProcessorsConfig' => [
                    'css_preprocessor' => [
                        'class' => 'Magento\Css\PreProcessor\Composite',
                        'asset_type' => 'css'
                    ],
                    'css_preprocessor2' => [
                        'class' => 'Magento\Css\PreProcessor\Composite2',
                        'asset_type' => 'css'
                    ],
                ],
                'createMap' => [
                    'Magento\Css\PreProcessor\Composite' => 'expected',
                    'Magento\Css\PreProcessor\Composite2' => 'expected'
                ],
                'expectedResult' => 'result_source_path'
            ],
            'one processor for css' => [
                'extension' => 'css',
                'preProcessorsConfig' => [
                    'css_preprocessor' => [
                        'class' => 'Magento\Css\PreProcessor\Composite',
                        'asset_type' => 'css'
                    ],
                ],
                'createMap' => [
                    'Magento\Css\PreProcessor\Composite' => 'expected',
                ],
                'expectedResult' => 'result_source_path_one'
            ],
            'one processor for css with no result' => [
                'extension' => 'css',
                'preProcessorsConfig' => [
                    'css_preprocessor' => [
                        'class' => 'Magento\Css\PreProcessor\Composite',
                        'asset_type' => 'css'
                    ],
                ],
                'createMap' => [
                    'Magento\Css\PreProcessor\Composite' => 'expected',
                ],
                'expectedResult' => null
            ],
            'no processors' => [
                'extension' => 'css',
                'preProcessorsConfig' => [],
                'createMap' => [],
                'expectedResult' => null
            ],
            'one processor for xyz' => [
                'extension' => 'css',
                'preProcessorsConfig' => [
                    'css_preprocessor' => [
                        'class' => 'Magento\Css\PreProcessor\Composite',
                        'asset_type' => 'xyz'
                    ],
                ],
                'createMap' => [
                    'Magento\Css\PreProcessor\Composite' => 'not expected',
                ],
                'expectedResult' => null
            ],

        ];
    }
}
