<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_LoadTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Core\Model\Layout */
    protected $_layout = null;

    /** @var \Magento\AdvancedCheckout\Block\Adminhtml\Manage\Load */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = Mage::getModel('\Magento\Core\Model\Layout');
        $this->_block = $this->_layout->createBlock('\Magento\AdvancedCheckout\Block\Adminhtml\Manage\Load');
    }

    public function testToHtml()
    {
        $blockName        = 'block1';
        $blockNameOne     = 'block2';
        $containerName    = 'container';
        $content          = 'Content 1';
        $contentOne       = 'Content 2';
        $containerContent = 'Content in container';

        $parent = $this->_block->getNameInLayout();
        $this->_layout->addBlock('\Magento\Core\Block\Text', $blockName, $parent)->setText($content);
        $this->_layout->addContainer($containerName, 'Container', array(), $parent);
        $this->_layout->addBlock('\Magento\Core\Block\Text', '', $containerName)->setText($containerContent);
        $this->_layout->addBlock('\Magento\Core\Block\Text', $blockNameOne, $parent)->setText($contentOne);

        $result = $this->_block->toHtml();
        $expectedDecoded = array(
            $blockName       => $content,
            $containerName   => $containerContent,
            $blockNameOne    => $contentOne
        );
        $this->assertEquals($expectedDecoded, Mage::helper('Magento\Core\Helper\Data')->jsonDecode($result));
    }
}
