<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Asset;

class PropertyGroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\PropertyGroup
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = new \Magento\View\Asset\PropertyGroup(array('test_property' => 'test_value'));
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
