<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Address\Config;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Address\Config\Reader
     */
    protected $_model;

    /**
     * @var \Magento\Framework\Config\FileResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileResolverMock;

    /**
     * @var \Magento\Customer\Model\Address\Config\Converter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converter;

    /**
     * @var \Magento\Customer\Model\Address\Config\SchemaLocator
     */
    protected $_schemaLocator;

    /**
     * @var \Magento\Framework\Config\ValidationStateInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_validationState;

    protected function setUp()
    {
        $this->_fileResolverMock = $this->getMock('Magento\Framework\Config\FileResolverInterface');
        $this->_fileResolverMock->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            'address_formats.xml',
            'scope'
        )->will(
            $this->returnValue(
                array(
                    file_get_contents(__DIR__ . '/_files/formats_one.xml'),
                    file_get_contents(__DIR__ . '/_files/formats_two.xml')
                )
            )
        );

        $this->_converter = $this->getMock('Magento\Customer\Model\Address\Config\Converter', array('convert'));

        $moduleReader = $this->getMock(
            'Magento\Framework\Module\Dir\Reader',
            array('getModuleDir'),
            array(),
            '',
            false
        );

        $moduleReader->expects(
            $this->once()
        )->method(
            'getModuleDir'
        )->with(
            'etc',
            'Magento_Customer'
        )->will(
            $this->returnValue('stub')
        );

        $this->_schemaLocator = new \Magento\Customer\Model\Address\Config\SchemaLocator($moduleReader);
        $this->_validationState = $this->getMock('Magento\Framework\Config\ValidationStateInterface');
        $this->_validationState->expects($this->once())->method('isValidated')->will($this->returnValue(false));

        $this->_model = new \Magento\Customer\Model\Address\Config\Reader(
            $this->_fileResolverMock,
            $this->_converter,
            $this->_schemaLocator,
            $this->_validationState
        );
    }

    public function testRead()
    {
        $expectedResult = new \stdClass();
        $constraint = function (\DOMDocument $actual) {
            try {
                $expected = __DIR__ . '/_files/formats_merged.xml';
                \PHPUnit_Framework_Assert::assertXmlStringEqualsXmlFile($expected, $actual->saveXML());
                return true;
            } catch (\PHPUnit_Framework_AssertionFailedError $e) {
                return false;
            }
        };

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
}
