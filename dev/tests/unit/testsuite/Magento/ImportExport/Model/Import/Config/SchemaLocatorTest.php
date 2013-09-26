<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Import\Config;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReaderMock;

    /**
     * @var \Magento\ImportExport\Model\Import\Config\SchemaLocator
     */
    protected $_model;

    protected function setUp()
    {
        $this->_moduleReaderMock = $this->getMock(
            'Magento\Core\Model\Config\Modules\Reader', array(), array(), '', false
        );

        $this->_moduleReaderMock->expects($this->any())
            ->method('getModuleDir')->with('etc', 'Magento_ImportExport')->will($this->returnValue('schema_dir'));
        $this->_model = new \Magento\ImportExport\Model\Import\Config\SchemaLocator($this->_moduleReaderMock);
    }

    public function testGetSchema()
    {
        $this->assertEquals('schema_dir' . DIRECTORY_SEPARATOR . 'import_merged.xsd', $this->_model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $this->assertEquals('schema_dir' . DIRECTORY_SEPARATOR . 'import.xsd', $this->_model->getPerFileSchema());
    }
}
