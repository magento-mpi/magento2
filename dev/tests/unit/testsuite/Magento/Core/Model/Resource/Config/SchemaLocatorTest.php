<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource\Config;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modulesReaderMock;

    /**
     * @var \Magento\Core\Model\Resource\Config\SchemaLocator
     */
    protected $_model;

    protected function setUp()
    {
        $this->_modulesReaderMock = $this->getMock(
            'Magento\Core\Model\Config\Modules\Reader', array(), array(), '', false
        );

        $this->_modulesReaderMock->expects($this->once())
            ->method('getModuleDir')
            ->with('etc', 'Magento_Core')
            ->will($this->returnValue('some_path'));

        $this->_model = new \Magento\Core\Model\Resource\Config\SchemaLocator($this->_modulesReaderMock);
    }

    /**
     * @covers \Magento\Core\Model\Resource\Config\SchemaLocator::getSchema
     */
    public function testGetSchema()
    {
        $expectedSchemaPath = 'some_path' . DIRECTORY_SEPARATOR . 'resources.xsd';
        $this->assertEquals($expectedSchemaPath, $this->_model->getSchema());
    }

    /**
     * @covers \Magento\Core\Model\Resource\Config\SchemaLocator::getPerFileSchema
     */
    public function testGetPerFileSchema()
    {
        $this->assertNull($this->_model->getPerFileSchema());
    }
}
