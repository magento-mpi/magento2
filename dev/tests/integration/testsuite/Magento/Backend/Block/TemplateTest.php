<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Backend_Block_Template.
 *
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Block_TemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Block_Template
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Backend_Block_Template');
    }

    /**
     * @covers Magento_Backend_Block_Template::getFormKey
     */
    public function testGetFormKey()
    {
        $this->assertGreaterThan(15, strlen($this->_block->getFormKey()));
    }

    /**
     * @covers Magento_Backend_Block_Template::isOutputEnabled
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
