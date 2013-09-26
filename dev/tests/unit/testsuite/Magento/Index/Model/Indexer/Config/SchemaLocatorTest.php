<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Model\Indexer\Config;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Index\Model\Indexer\Config\SchemaLocator
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modulesReaderMock;

    protected function setUp()
    {
        $this->_modulesReaderMock = $this->getMock(
            'Magento\Core\Model\Config\Modules\Reader', array(), array(), '', false
        );

        $this->_modulesReaderMock->expects($this->once())
            ->method('getModuleDir')
            ->with('etc', 'Magento_Index')
            ->will($this->returnValue('some_path'));

        $this->_model = new \Magento\Index\Model\Indexer\Config\SchemaLocator($this->_modulesReaderMock);
    }

    /**
     * @covers \Magento\Index\Model\Indexer\Config\SchemaLocator::getSchema
     */
    public function testGetSchema()
    {
        $expectedSchema = 'some_path' . DIRECTORY_SEPARATOR . 'indexers.xsd';
        $this->assertEquals($expectedSchema, $this->_model->getSchema());
    }

    /**
     * @covers \Magento\Index\Model\Indexer\Config\SchemaLocator::getPerFileSchema
     */
    public function testGetPerFileSchema()
    {
        $this->assertEquals(null, $this->_model->getPerFileSchema());
    }
}
