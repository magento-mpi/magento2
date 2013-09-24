<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Cron_Model_Config_Reader_XmlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Cron_Model_Config_Reader_Xml
     */
    protected $_xmlReader;

    /**
     * Prepare parameters
     */
    public function setUp()
    {
        $fileResolver = $this->getMockBuilder('Magento_Core_Model_Config_FileResolver')
            ->disableOriginalConstructor()
            ->getMock();
        $converter = $this->getMockBuilder('Magento_Cron_Model_Config_Converter_Xml')
            ->disableOriginalConstructor()
            ->getMock();
        $schema = $this->getMockBuilder('Magento_Cron_Model_Config_SchemaLocator')
            ->disableOriginalConstructor()
            ->getMock();
        $validator = $this->getMockBuilder('Magento_Core_Model_Config_ValidationState')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_xmlReader = new Magento_Cron_Model_Config_Reader_Xml($fileResolver, $converter, $schema, $validator);
    }

    /**
     * Test creating object
     */
    public function testInstanceof()
    {
        $this->assertInstanceOf('Magento_Cron_Model_Config_Reader_Xml', $this->_xmlReader);
    }
}
