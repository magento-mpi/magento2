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
        $this->_filePath = 'code/Magento/Cron/etc/crontab.xsd';
        $this->_locator = new Magento_Cron_Model_Config_SchemaLocator();
    }

    /**
     * Testing that schema has file
     */
    public function testGetSchema()
    {
        $result = $this->_locator->getSchema();
        $this->assertEquals($this->_filePath, $result);
    }
}
