<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\Placeholder\Config;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReaderMock;

    /**
     * @var \Magento\FullPageCache\Model\Placeholder\Config\SchemaLocator
     */
    protected $_model;

    protected function setUp()
    {
        $this->_moduleReaderMock = $this->getMock(
            'Magento\Module\Dir\Reader',
            array(), array(), '', false
        );
        $this->_moduleReaderMock
            ->expects($this->once())
            ->method('getModuleDir')
            ->with('etc', 'Magento_FullPageCache')
            ->will($this->returnValue('schema_dir'));
        $this->_model = new \Magento\FullPageCache\Model\Placeholder\Config\SchemaLocator($this->_moduleReaderMock);
    }

    public function testGetSchema()
    {
        $this->assertEquals('schema_dir/placeholders_merged.xsd', $this->_model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $this->assertEquals('schema_dir/placeholders.xsd', $this->_model->getPerFileSchema());
    }
}
