<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Model\Asset;

class PropertyGroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Page\Model\Asset\PropertyGroup
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = new \Magento\Page\Model\Asset\PropertyGroup(array('test_property' => 'test_value'));
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
