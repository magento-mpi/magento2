<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Test\Tools\Dependency\Report\Builder;

class AbstractBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Dependency\ParserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dependenciesParserMock;

    /**
     * @var \Magento\Tools\Dependency\Report\WriterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reportWriterMock;

    /**
     * @var \Magento\Tools\Dependency\Report\Builder\AbstractBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $builder;

    protected function setUp()
    {
        $this->dependenciesParserMock = $this->getMock('Magento\Tools\Dependency\ParserInterface');
        $this->reportWriterMock = $this->getMock('Magento\Tools\Dependency\Report\WriterInterface');

        $this->builder = $this->getMockForAbstractClass('Magento\Tools\Dependency\Report\Builder\AbstractBuilder', [
            'dependenciesParser' => $this->dependenciesParserMock,
            'reportWriter' => $this->reportWriterMock,
        ]);
    }

    /**
     * @param array $options
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Passed option "files_for_parse" is wrong.
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
            [['report_filename' => 'some_filename']],
            [['files_for_parse' => [], 'report_filename' => 'some_filename']],
        ];
    }

    /**
     * @param array $options
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Passed option "report_filename" is wrong.
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
            [['files_for_parse' => [1, 2]]],
            [['files_for_parse' => [1, 2], 'report_filename' => '']],
        ];
    }

    public function testBuild()
    {
        $options = ['files_for_parse' => [1, 2, 3], 'report_filename' => 'some_filename'];
        $parseResult = ['foo', 'bar', 'baz'];
        $configMock = $this->getMock('\Magento\Tools\Dependency\Report\Data\ConfigInterface');

        $this->dependenciesParserMock->expects($this->once())->method('parse')->with($options['files_for_parse'])
            ->will($this->returnValue($parseResult));
        $this->builder->expects($this->once())->method('prepareData')->with($parseResult)
            ->will($this->returnValue($configMock));
        $this->reportWriterMock->expects($this->once())->method('write')
            ->with($options['report_filename'], $configMock);

        $this->builder->build($options);
    }
}
