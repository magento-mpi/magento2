<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Test\Tools\Dependency\Report;

use Magento\TestFramework\Helper\ObjectManager;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Dependency\Report\Builder
     */
    protected $builder;

    /**
     * @var \Magento\Tools\Dependency\ParserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dependenciesParserMock;

    /**
     * @var \Magento\Tools\Dependency\Report\WriterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reportWriterMock;

    protected function setUp()
    {
        $this->dependenciesParserMock = $this->getMock('Magento\Tools\Dependency\ParserInterface');
        $this->reportWriterMock = $this->getMock('Magento\Tools\Dependency\Report\WriterInterface');

        $objectManagerHelper = new ObjectManager($this);
        $this->builder = $objectManagerHelper->getObject('Magento\Tools\Dependency\Report\Builder', [
            'dependenciesParser' => $this->dependenciesParserMock,
            'reportWriter' => $this->reportWriterMock,
        ]);
    }

    /**
     * @param array $options
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Passed option "configFiles" is wrong.
     * @dataProvider dataProviderWrongOptionConfigFiles
     */
    public function testBuildWithIfPassedFilesIsWrong($options)
    {
        $this->builder->build($options);
    }

    /**
     * @return array
     */
    public function dataProviderWrongOptionConfigFiles()
    {
        return [
            [['filename' => 'filename']],
            [['configFiles' => [], 'filename' => 'some_filename']],
        ];
    }

    /**
     * @param array $options
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Passed option "filename" is wrong.
     * @dataProvider dataProviderWrongOptionFilename
     */
    public function testBuildWithIfPassedFilename($options)
    {
        $this->builder->build($options);
    }

    /**
     * @return array
     */
    public function dataProviderWrongOptionFilename()
    {
        return [
            [['configFiles' => [1, 2]]],
            [['configFiles' => [1, 2], 'filename' => '']],
        ];
    }

    public function testBuild()
    {
        $options = ['configFiles' => [1, 2, 3], 'filename' => 'some_filename'];
        $parseResult = $this->getMock('Magento\Tools\Dependency\Config', [], [], '', false);

        $this->dependenciesParserMock->expects($this->once())->method('parse')->with($options['configFiles'])
            ->will($this->returnValue($parseResult));
        $this->reportWriterMock->expects($this->once())->method('write')->with($parseResult, $options['filename']);

        $this->builder->build($options);
    }
}
