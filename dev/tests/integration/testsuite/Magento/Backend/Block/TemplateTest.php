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
        $this->_block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_Backend_Block_Template');
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
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore()->setConfig('advanced/modules_disable_output/dummy', 'true');
        $this->assertFalse($this->_block->isOutputEnabled());

        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore()->setConfig('advanced/modules_disable_output/dummy', 'false');
        $this->assertTrue($this->_block->isOutputEnabled('dummy'));
    }
}
