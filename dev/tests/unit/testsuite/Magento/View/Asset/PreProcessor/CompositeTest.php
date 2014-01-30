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
     * @var array
     */
    protected $callMap = [];

    protected function setUp()
    {
        $this->preProcessorFactoryMock = $this->getMock('Magento\View\Asset\PreProcessorFactory', [], [], '', false);
        $this->objectManagerHelper = new ObjectManagerHelper($this);
    }

    /**
     * @param array $params
     * @param array $preProcessorsConfig
     * @param array $createMap
     * @param string $expectedResult
     * @dataProvider processDataProvider
     */
    public function testProcess($params, $preProcessorsConfig, $createMap, $expectedResult)
    {
        $this->composite = $this->objectManagerHelper->getObject(
            'Magento\View\Asset\PreProcessor\Composite',
            [
                'preProcessorFactory' => $this->preProcessorFactoryMock,
                'preProcessorsConfig' => $preProcessorsConfig
            ]
        );

        $targetDir = $this->getMock($params['targetDirectory'], array(), array(), '', false);

        foreach ($createMap as $className) {
            $this->callMap[$className] = $this->getMock($className, array('process'), array(), '', false);
            $this->callMap[$className]->expects($this->once())
                ->method('process')
                ->with(
                    $this->equalTo($params['filePath']),
                    $this->equalTo($params['params']),
                    $this->equalTo($targetDir),
                    $this->equalTo($params['sourcePath'])
                )
                ->will($this->returnValue($expectedResult));
        }

        $this->preProcessorFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(array($this, 'createProcessor')));

        $result = $this->composite->process(
            $params['filePath'],
            $params['params'],
            $targetDir,
            $params['sourcePath']
        );
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Create pre-processor callback
     *
     * @param string $className
     * @return mixed
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
                'params' => [
                    'filePath' => '/some/file/path.css',
                    'params' => ['theme' => 'some_theme', 'area' => 'frontend'],
                    'targetDirectory' => 'Magento\Filesystem\Directory\WriteInterface',
                    'sourcePath' => 'result_source_path'
                ],
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
                    'Magento\Css\PreProcessor\Composite',
                    'Magento\Css\PreProcessor\Composite2'
                ],
                'expectedResult' => 'result_source_path'
            ],
            'one processor for css' => [
                'params' => [
                    'filePath' => '/some/file/path_one.css',
                    'params' => ['theme' => 'some_theme', 'area' => 'frontend'],
                    'targetDirectory' => 'Magento\Filesystem\Directory\WriteInterface',
                    'sourcePath' => 'result_source_path_one'
                ],
                'preProcessorsConfig' => [
                    'css_preprocessor' => [
                        'class' => 'Magento\Css\PreProcessor\Composite',
                        'asset_type' => 'css'
                    ],
                ],
                'createMap' => [
                    'Magento\Css\PreProcessor\Composite',
                ],
                'expectedResult' => 'result_source_path_one'
            ],
            'one processor for css with no result' => [
                'params' => [
                    'filePath' => '/some/file/path_one.css',
                    'params' => ['theme' => 'some_theme', 'area' => 'frontend'],
                    'targetDirectory' => 'Magento\Filesystem\Directory\WriteInterface',
                    'sourcePath' => null
                ],
                'preProcessorsConfig' => [
                    'css_preprocessor' => [
                        'class' => 'Magento\Css\PreProcessor\Composite',
                        'asset_type' => 'css'
                    ],
                ],
                'createMap' => [
                    'Magento\Css\PreProcessor\Composite',
                ],
                'expectedResult' => null
            ],
            'no processors' => [
                'params' => [
                    'filePath' => '/some/file/path.css',
                    'params' => ['theme' => 'some_theme', 'area' => 'frontend'],
                    'targetDirectory' => 'Magento\Filesystem\Directory\WriteInterface',
                    'sourcePath' => null
                ],
                'preProcessorsConfig' => [],
                'createMap' => [],
                'expectedResult' => null
            ],
        ];
    }
}
