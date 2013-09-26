<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Attribute\Config;

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    const FIXTURE_XSD_DIR   = 'fixture_dir';
    const FIXTURE_XSD_FILE  = 'fixture_dir/catalog_attributes.xsd';

    /**
     * @var \Magento\Catalog\Model\Attribute\Config\SchemaLocator
     */
    protected $_model;

    /**
     * @var \Magento\Core\Model\Config\Modules\Reader|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReader;

    protected function setUp()
    {
        $this->_moduleReader = $this->getMock(
            'Magento\Core\Model\Config\Modules\Reader', array('getModuleDir'), array(), '', false
        );
        $this->_moduleReader
            ->expects($this->once())
            ->method('getModuleDir')->with('etc', 'Magento_Catalog')
            ->will($this->returnValue(self::FIXTURE_XSD_DIR))
        ;
        $this->_model = new \Magento\Catalog\Model\Attribute\Config\SchemaLocator($this->_moduleReader);
    }

    public function testGetSchema()
    {
        $this->assertEquals(self::FIXTURE_XSD_FILE, $this->_model->getSchema());
        // Makes sure the value is calculated only once
        $this->assertEquals(self::FIXTURE_XSD_FILE, $this->_model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $this->assertEquals(self::FIXTURE_XSD_FILE, $this->_model->getPerFileSchema());
        // Makes sure the value is calculated only once
        $this->assertEquals(self::FIXTURE_XSD_FILE, $this->_model->getPerFileSchema());
    }
}
