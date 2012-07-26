<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Block_Template.
 *
 * @group module:Mage_Backend
 */
class Mage_Backend_Block_TemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Block_Template
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = new Mage_Backend_Block_Template;
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    /**
     * @covers Mage_Backend_Block_Template::getFormKey
     */
    public function testGetFormKey()
    {
        $this->assertGreaterThan(15, strlen($this->_block->getFormKey()));
    }

    /**
     * @covers Mage_Backend_Block_Template::isOutputEnabled
     */
    public function testIsOutputEnabled()
    {
        $this->_block->setData('module_name', 'dummy');
        Mage::app()->getStore()->setConfig('advanced/modules_disable_output/dummy', 'true');
        $this->assertFalse($this->_block->isOutputEnabled());

        Mage::app()->getStore()->setConfig('advanced/modules_disable_output/dummy', 'false');
        $this->assertTrue($this->_block->isOutputEnabled('dummy'));


    }
}
