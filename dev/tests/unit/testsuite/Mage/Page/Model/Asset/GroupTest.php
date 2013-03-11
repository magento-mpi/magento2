<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Page_Model_Asset_GroupTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Page_Model_Asset_Group
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = new Mage_Page_Model_Asset_Group(array('test_property' => 'test_value'));
    }

    public function testGetProperties()
    {
        $this->assertEquals(array('test_property' => 'test_value'), $this->_object->getProperties());
    }

    public function testGetProperty()
    {
        $this->assertEquals('test_value', $this->_object->getProperty('test_property'));
        $this->assertNull($this->_object->getProperty('non_existing_property'));
    }
}
