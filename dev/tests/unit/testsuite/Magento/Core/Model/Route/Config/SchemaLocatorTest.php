<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Route_Config_SchemaLocatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReaderMock;

    /**
     * @var \Magento\Core\Model\Route\Config\SchemaLocator
     */
    protected $_model;

    protected function setUp()
    {
        $this->_moduleReaderMock = $this->getMock(
            'Magento\Core\Model\Config\Modules\Reader', array(), array(), '', false
        );
        $this->_moduleReaderMock->expects($this->any())
            ->method('getModuleDir')->with('etc', 'Magento_Core')->will($this->returnValue('schema_dir'));
        $this->_model = new \Magento\Core\Model\Route\Config\SchemaLocator($this->_moduleReaderMock);
    }

    public function testGetSchema()
    {
        $this->assertEquals('schema_dir' . DIRECTORY_SEPARATOR . 'routes_merged.xsd', $this->_model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $this->assertEquals('schema_dir' . DIRECTORY_SEPARATOR . 'routes.xsd', $this->_model->getPerFileSchema());
    }
}
