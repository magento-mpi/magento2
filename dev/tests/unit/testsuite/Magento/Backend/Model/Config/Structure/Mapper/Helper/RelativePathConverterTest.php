<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Structure\Mapper\Helper;

class RelativePathConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Config\Structure\Mapper\Helper\RelativePathConverter
     */
    protected $_sut;

    protected function setUp()
    {
        $this->_sut = new \Magento\Backend\Model\Config\Structure\Mapper\Helper\RelativePathConverter();
    }

    public function testConvertWithInvalidRelativePath()
    {
        $nodePath = 'node/path';
        $relativePath = '*/*/*/relativePath';

        $exceptionMessage = sprintf('Invalid relative path %s in %s node', $relativePath, $nodePath);

        $this->setExpectedException('InvalidArgumentException', $exceptionMessage);
        $this->_sut->convert($nodePath, $relativePath);
    }

    /**
     * @dataProvider testConvertWithInvalidArgumentsDataProvider
     * @param string $nodePath
     * @param string $relativePath
     */
    public function testConvertWithInvalidArguments($nodePath, $relativePath)
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid arguments');
        $this->_sut->convert($nodePath, $relativePath);
    }

    /**
     * @dataProvider testConvertDataProvider
     * @param string $nodePath
     * @param string $relativePath
     * @param string $result
     */
    public function testConvert($nodePath, $relativePath, $result)
    {
        $this->assertEquals($result, $this->_sut->convert($nodePath, $relativePath));
    }

    public function testConvertWithInvalidArgumentsDataProvider()
    {
        return array(array('', ''), array('some/node', ''), array('', 'some/node'));
    }

    public function testConvertDataProvider()
    {
        return array(
            array('currentNode', 'relativeNode', 'relativeNode'),
            array('current/node/path', 'relative/node/path', 'relative/node/path'),
            array('current/node', 'siblingRelativeNode', 'current/siblingRelativeNode'),
            array('current/node', '*/siblingNode', 'current/siblingNode'),
            array('very/deep/node/hierarchy', '*/*/sourceNode', 'very/deep/sourceNode')
        );
    }
}
