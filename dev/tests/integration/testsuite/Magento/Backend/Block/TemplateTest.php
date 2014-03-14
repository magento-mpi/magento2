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

namespace Magento\Backend\Block;

/**
 * Test class for \Magento\Backend\Block\Template.
 *
 * @magentoAppArea adminhtml
 */
class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Block\Template
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Backend\Block\Template');
    }

    /**
     * @covers \Magento\Backend\Block\Template::getFormKey
     */
    public function testGetFormKey()
    {
        $this->assertGreaterThan(15, strlen($this->_block->getFormKey()));
    }

    /**
     * @covers \Magento\Backend\Block\Template::isOutputEnabled
     */
    public function testIsOutputEnabled()
    {
        $this->_block->setData('module_name', 'dummy');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore()->setConfig('advanced/modules_disable_output/dummy', 'true');
        $this->assertFalse($this->_block->isOutputEnabled());

        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore()->setConfig('advanced/modules_disable_output/dummy', 'false');
        $this->assertTrue($this->_block->isOutputEnabled('dummy'));
    }
}
