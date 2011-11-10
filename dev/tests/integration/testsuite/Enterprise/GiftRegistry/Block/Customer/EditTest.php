<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftRegistry
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_GiftRegistry
 */
class Enterprise_GiftRegistry_Block_Customer_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_GiftRegistry_Block_Customer_Edit
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = new Enterprise_GiftRegistry_Block_Customer_Edit;
    }

    public function testAddInputTypeTemplate()
    {
        $this->assertEmpty($this->_block->getInputTypeTemplate('test'));
        $this->_block->addInputTypeTemplate('test', 'Enterprise_GiftRegistry::attributes/text.phtml');
        $template = $this->_block->getInputTypeTemplate('test');
        $this->assertFileExists($template);
        $this->assertStringEndsWith('attributes' . DIRECTORY_SEPARATOR . 'text.phtml', $template);
    }
}
