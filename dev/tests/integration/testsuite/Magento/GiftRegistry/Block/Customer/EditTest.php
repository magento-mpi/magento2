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

namespace Magento\GiftRegistry\Block\Customer;

class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Block\Customer\Edit
     */
    protected $_block;

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')->setAreaCode('frontend');
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\GiftRegistry\Block\Customer\Edit');
    }

    public function testAddInputTypeTemplate()
    {
        $this->assertEmpty($this->_block->getInputTypeTemplate('test'));
        $this->_block->addInputTypeTemplate('test', 'Magento_GiftRegistry::attributes/text.phtml');
        $template = $this->_block->getInputTypeTemplate('test');
        $this->assertFileExists($template);
        $this->assertStringEndsWith('attributes/text.phtml', $template);
    }
}
