<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Asset\PreProcessor;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class CompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\Asset\PreProcessor\Composite
     */
    protected $composite;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\View\Asset\PreProcessorFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $preProcessorFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $callMap = array();

    protected function setUp()
    {
        $this->preProcessorFactoryMock = $this->getMock(
            'Magento\Framework\View\Asset\PreProcessorFactory',
            array(),
            array(),
            '',
            false
        );
        $this->objectManagerHelper = new ObjectManagerHelper($this);
    }

    /**
     * @param array $extension
     * @param array $preProcessorsConfig
     * @param array $createMap
     * @dataProvider processDataProvider
     */
    public function testProcess($extension, $preProcessorsConfig, $createMap)
    {
        $this->composite = $this->objectManagerHelper->getObject(
            'Magento\Framework\View\Asset\PreProcessor\Composite',
            array(
                'preProcessorFactory' => $this->preProcessorFactoryMock,
                'preProcessorsConfig' => $preProcessorsConfig
            )
        );

        $publisherFile = $this->getMock('Magento\Framework\View\Publisher\CssFile', array(), array(), '', false);
        $publisherFile->expects($this->once())->method('getExtension')->will($this->returnValue($extension));

        $targetDir = $this->getMock(
            'Magento\Framework\Filesystem\Directory\WriteInterface',
            array(),
            array(),
            '',
            false
        );

        foreach ($createMap as $className => $isExpected) {
            $this->callMap[$className] = $this->getMock($className, array('process'), array(), '', false);

            if ($isExpected === 'expected') {
                $this->callMap[$className]->expects(
                    $this->once()
                )->method(
                    'process'
                )->with(
                    $this->equalTo($publisherFile),
                    $this->equalTo($targetDir)
                )->will(
                    $this->returnValue($publisherFile)
                );
            } else {
                $this->callMap[$className]->expects($this->never())->method('process');
            }
        }

        $this->preProcessorFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnCallback(array($this, 'createProcessor'))
        );

        $this->assertEquals($publisherFile, $this->composite->process($publisherFile, $targetDir));
    }

    /**
     * Create pre-processor callback
     *
     * @param string $className
     * @return \Magento\Framework\View\Asset\PreProcessor\PreProcessorInterface[]
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
        return array(
            'list of processors for css' => array(
                'extension' => 'css',
                'preProcessorsConfig' => array(
                    'css_preprocessor' => array(
                        'class' => 'Magento\Framework\Css\PreProcessor\Composite',
                        'asset_type' => 'css'
                    ),
                    'css_preprocessor2' => array(
                        'class' => 'Magento\Framework\Css\PreProcessor\Composite2',
                        'asset_type' => 'css'
                    )
                ),
                'createMap' => array(
                    'Magento\Framework\Css\PreProcessor\Composite' => 'expected',
                    'Magento\Framework\Css\PreProcessor\Composite2' => 'expected'
                )
            ),
            'one processor for css' => array(
                'extension' => 'css',
                'preProcessorsConfig' => array(
                    'css_preprocessor' => array(
                        'class' => 'Magento\Framework\Css\PreProcessor\Composite',
                        'asset_type' => 'css'
                    )
                ),
                'createMap' => array('Magento\Framework\Css\PreProcessor\Composite' => 'expected')
            ),
            'no processors' => array('extension' => 'css', 'preProcessorsConfig' => array(), 'createMap' => array()),
            'one processor for xyz' => array(
                'extension' => 'css',
                'preProcessorsConfig' => array(
                    'css_preprocessor' => array(
                        'class' => 'Magento\Framework\Css\PreProcessor\Composite',
                        'asset_type' => 'xyz'
                    )
                ),
                'createMap' => array('Magento\Framework\Css\PreProcessor\Composite' => 'not expected')
            )
        );
    }
}
