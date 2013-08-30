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
    /** @var Magento_Core_Model_Layout */
    protected $_layout = null;

    /** @var Magento_AdvancedCheckout_Block_Adminhtml_Manage_Load */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = Mage::getModel('Magento_Core_Model_Layout');
        $this->_block = $this->_layout->createBlock('Magento_AdvancedCheckout_Block_Adminhtml_Manage_Load');
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
        $this->_layout->addBlock('Magento_Core_Block_Text', $blockName, $parent)->setText($content);
        $this->_layout->addContainer($containerName, 'Container', array(), $parent);
        $this->_layout->addBlock('Magento_Core_Block_Text', '', $containerName)->setText($containerContent);
        $this->_layout->addBlock('Magento_Core_Block_Text', $blockNameOne, $parent)->setText($contentOne);

        $result = $this->_block->toHtml();
        $expectedDecoded = array(
            $blockName       => $content,
            $containerName   => $containerContent,
            $blockNameOne    => $contentOne
        );
        $this->assertEquals($expectedDecoded, Mage::helper('Magento_Core_Helper_Data')->jsonDecode($result));
    }
}
