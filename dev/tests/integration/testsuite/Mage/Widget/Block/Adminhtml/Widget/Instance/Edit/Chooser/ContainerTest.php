<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_ContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Container
     */
    protected $_block = null;

    protected function setUp()
    {
        $this->_block = new Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Container;
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testSetGetAllowedContainers()
    {
        $this->assertEmpty($this->_block->getAllowedContainers());
        $containers = array('some_container', 'another_container');
        $this->_block->setAllowedContainers($containers);
        $this->assertEquals($containers, $this->_block->getAllowedContainers());
    }
}
