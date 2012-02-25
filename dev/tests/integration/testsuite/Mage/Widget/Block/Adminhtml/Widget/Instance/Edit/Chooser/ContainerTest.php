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

/**
 * @group module:Mage_Widget
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

    public function testSetGetAllowedContainers()
    {
        $this->assertSame(array(), $this->_block->getAllowedContainers());
        $this->assertSame(1, $this->_block->setAllowedContainers(1)->getAllowedContainers());
    }

    public function testGetContainers()
    {
        $this->_block->setLayoutHandle('default');
        $result = $this->_block->getContainers();
        $this->assertArrayHasKey(0, $result);
        $this->assertTrue(count($result) > 1);
        list($key, $value) = each($result);
        $this->assertSame(0, $key);
        $this->assertSame(array('value' => '', 'label' => '-- Please Select --'), $value);
        while (list($key, $value) = each($pleaseSelect)) {
            $this->assertInternalType('string', $key);
            $this->assertNotEmpty($key);
            $this->assertInternalType('string', $value);
            $this->assertNotEmpty($value);
        }
        $this->_block->setLayoutHandle('wrong handle, but it is ignored because containers are already collected');
        $this->assertSame($result, $this->_block->getContainers());
    }
}
