<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Event\Config;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReaderMock;

    /**
     * @var \Magento\Event\Config\SchemaLocator
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Event\Config\SchemaLocator();
    }

    public function testGetSchema()
    {
        $expected = BP . str_replace('\\', DIRECTORY_SEPARATOR, '\lib\Magento\Event\etc\events.xsd');
        $actual = $this->_model->getSchema();
        $this->assertEquals($expected, $actual);

    }

    public function testGetPerFileSchema()
    {
        $actual = $this->_model->getPerFileSchema();
        $expected = BP . str_replace('\\', DIRECTORY_SEPARATOR, '\lib\Magento\Event\etc\events.xsd');
        $this->assertEquals($expected, $actual);
    }
}
