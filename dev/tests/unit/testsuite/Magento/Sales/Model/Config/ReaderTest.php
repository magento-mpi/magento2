<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Config_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Sales_Model_Config_Reader
     */
    protected $_reader;

    /**
     * Prepare parameters
     */
    public function setUp()
    {
        $fileResolver = $this->getMockBuilder('Magento_Core_Model_Config_FileResolver')
            ->disableOriginalConstructor()
            ->getMock();
        $converter = $this->getMockBuilder('Magento_Sales_Model_Config_Converter')
            ->disableOriginalConstructor()
            ->getMock();
        $schema = $this->getMockBuilder('Magento_Sales_Model_Config_SchemaLocator')
            ->disableOriginalConstructor()
            ->getMock();
        $validator = $this->getMockBuilder('Magento_Core_Model_Config_ValidationState')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_reader = new Magento_Sales_Model_Config_Reader($fileResolver, $converter, $schema, $validator);
    }

    /**
     * Test creating object
     */
    public function testInstanceof()
    {
        $this->assertInstanceOf('Magento_Sales_Model_Config_Reader', $this->_reader);
    }
}
