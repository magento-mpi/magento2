<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mview\Config;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Mview\Config\Reader
     */
    protected $_model;

    /**
     * @var \Magento\Mview\Config\Converter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converter;

    /**
     * @var \Magento\Framework\App\Config\FileResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileResolverMock;

    protected function setUp()
    {
        $this->_fileResolverMock = $this->getMock(
            'Magento\Framework\App\Config\FileResolver',
            array('get'),
            array(),
            '',
            false
        );

        $this->_converter = $this->getMock('Magento\Mview\Config\Converter', array('convert'));

        $fsDirList = $this->getMock('\Magento\Framework\Filesystem\DirectoryList', array('getDir'), array(), '', false);
        $fsDirList->expects(
            $this->once()
        )->method(
            'getDir'
        )->with(
            \Magento\Framework\App\Filesystem::LIB_DIR
        )->will(
            $this->returnValue('stub')
        );
        $schemaLocator = new \Magento\Mview\Config\SchemaLocator($fsDirList);

        $validationState = $this->getMock('Magento\Framework\Config\ValidationStateInterface');
        $validationState->expects($this->once())->method('isValidated')->will($this->returnValue(false));

        $this->_model = new \Magento\Mview\Config\Reader(
            $this->_fileResolverMock,
            $this->_converter,
            $schemaLocator,
            $validationState
        );
    }

    /**
     * @dataProvider readerDataProvider
     */
    public function testReadValidConfig($files, $expectedFile)
    {
        $this->_fileResolverMock->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            'mview.xml',
            'scope'
        )->will(
            $this->returnValue($files)
        );

        $constraint = function (\DOMDocument $actual) use ($expectedFile) {
            try {
                $expected = file_get_contents(__DIR__ . '/../_files/' . $expectedFile);
                \PHPUnit_Framework_Assert::assertXmlStringEqualsXmlString($expected, $actual->saveXML());
                return true;
            } catch (\PHPUnit_Framework_AssertionFailedError $e) {
                return false;
            }
        };
        $expectedResult = new \stdClass();
        $this->_converter->expects(
            $this->once()
        )->method(
            'convert'
        )->with(
            $this->callback($constraint)
        )->will(
            $this->returnValue($expectedResult)
        );

        $this->assertSame($expectedResult, $this->_model->read('scope'));
    }

    /**
     * @return array
     */
    public function readerDataProvider()
    {
        return array(
            'mview_merged_one' => array(
                array(
                    'mview_one.xml' => file_get_contents(__DIR__ . '/../_files/mview_one.xml'),
                    'mview_two.xml' => file_get_contents(__DIR__ . '/../_files/mview_two.xml')
                ),
                'mview_merged_one.xml'
            ),
            'mview_merged_two' => array(
                array(
                    'mview_one.xml' => file_get_contents(__DIR__ . '/../_files/mview_one.xml'),
                    'mview_three.xml' => file_get_contents(__DIR__ . '/../_files/mview_three.xml')
                ),
                'mview_merged_two.xml'
            )
        );
    }
}
