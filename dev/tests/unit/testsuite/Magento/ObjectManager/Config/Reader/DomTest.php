<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ObjectManager\Config\Reader;

require_once __DIR__ . '/_files/ConfigDomMock.php';

class DomTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $converterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $schemaLocatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $validationStateMock;

    /**
     * @var \Magento\ObjectManager\Config\Reader\Dom
     */
    protected $model;

    protected function setUp()
    {
        $this->fileResolverMock = $this->getMock('\Magento\Config\FileResolverInterface');
        $this->converterMock = $this->getMock('\Magento\ObjectManager\Config\Mapper\Dom', array(), array(), '', false);
        $this->schemaLocatorMock = $this->getMock(
            '\Magento\ObjectManager\Config\SchemaLocator',
            array(),
            array(),
            '',
            false
        );
        $this->validationStateMock = $this->getMock(
            '\Magento\Config\ValidationStateInterface',
            array(),
            array(),
            '',
            false
        );

        $this->model = new \Magento\ObjectManager\Config\Reader\Dom(
            $this->fileResolverMock,
            $this->converterMock,
            $this->schemaLocatorMock,
            $this->validationStateMock,
            'filename.xml',
            array(),
            '\ConfigDomMock'
        );
    }

    /**
     * @covers _createConfigMerger()
     */
    public function testRead()
    {
        $fileList = array('first content item');
        $this->fileResolverMock->expects($this->once())->method('get')->will($this->returnValue($fileList));
        $this->converterMock->expects($this->once())->method('convert')->with('reader dom result');
        $this->model->read();
    }
}
