<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Customer_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Block\Customer\Edit
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('\Magento\GiftRegistry\Block\Customer\Edit');
    }

    public function testAddInputTypeTemplate()
    {
        $this->assertEmpty($this->_block->getInputTypeTemplate('test'));
        $this->_block->addInputTypeTemplate('test', 'Magento_GiftRegistry::attributes/text.phtml');
        $template = $this->_block->getInputTypeTemplate('test');
        $this->assertFileExists($template);
        $this->assertStringEndsWith('attributes' . DIRECTORY_SEPARATOR . 'text.phtml', $template);
    }
}
