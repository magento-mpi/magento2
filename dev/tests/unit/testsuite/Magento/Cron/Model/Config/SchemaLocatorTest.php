<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Cron_Model_Config_SchemaLocatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Cron_Model_Config_SchemaLocator|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReader;

    /**
     * @var Magento_Cron_Model_Config_SchemaLocator
     */
    protected $_locator;

    /**
     * @var string
     */
    protected $_filePath;

    /**
     * Initialize parameters
     */
    protected function setUp()
    {
        $this->_moduleReader = $this->getMockBuilder('Magento_Core_Model_Config_Modules_Reader')
            ->disableOriginalConstructor()
            ->setMethods(array('getModuleDir'))
            ->getMock();
        $this->_filePath = 'path/to/Magento/Cron/etc';
        $this->_moduleReader->expects($this->once())
            ->method('getModuleDir')
            ->with($this->equalTo('etc'), $this->equalTo('Magento_Cron'))
            ->will($this->returnValue($this->_filePath));

        $this->_locator = new Magento_Cron_Model_Config_SchemaLocator($this->_moduleReader);
    }

    /**
     * Testing that schema has file
     */
    public function testGetSchema()
    {
        $result = $this->_locator->getSchema();
        $this->assertEquals($this->_filePath . DIRECTORY_SEPARATOR . 'crontab.xsd', $result);
    }
}
