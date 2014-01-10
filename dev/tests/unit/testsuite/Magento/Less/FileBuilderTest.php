<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Less;

class FileBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    protected  function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * @dataProvider prepareInstructionsDataProvider
     */
    public function testBuild($instructions, $expectedResult)
    {
        /** @var \Magento\Less\FileBuilder $fileBuilderMock */
        $fileBuilderMock = $this->objectManager->getObject(
            'Magento\Less\FileBuilder'
        );

        $method = new \ReflectionMethod('Magento\Less\FileBuilder', '_build');
        $method->setAccessible(true);
        $this->assertEquals($expectedResult, $method->invoke($fileBuilderMock, $instructions));
    }

    /**
     * @return array
     */
    public function prepareInstructionsDataProvider()
    {
        $imports = array();
        $rendererData = $this->rendererSampleData();

        foreach ($rendererData as $rendererValue) {
            $instruction = $this->getMock(
                'Magento\Less\Instruction\Import',
                array('render'),
                array(),
                '',
                false
            );

            $instruction->expects($this->any())
                ->method('render')
                ->will($this->returnValue($rendererValue));

            $imports[] = $instruction;
        }
        return array(
            'imports' => array(
                $imports,
                implode(PHP_EOL, $rendererData)
            )
        );
    }

    /**
     * @return array
     */
    protected function rendererSampleData()
    {
        return array(
            '//@magento_import "magento_import.less";',
            '@import "dir/import.less";',
            '@import "dir/custom.less";'
        );
    }
}
