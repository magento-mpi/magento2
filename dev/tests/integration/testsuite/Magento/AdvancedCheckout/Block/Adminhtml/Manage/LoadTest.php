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
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage;

/**
 * @magentoAppArea adminhtml
 */
class LoadTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\View\LayoutInterface */
    protected $_layout = null;

    /** @var \Magento\AdvancedCheckout\Block\Adminhtml\Manage\Load */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\LayoutInterface'
        );
        $this->_block = $this->_layout->createBlock('Magento\AdvancedCheckout\Block\Adminhtml\Manage\Load');
    }

    public function testToHtml()
    {
        $blockName = 'block1';
        $blockNameOne = 'block2';
        $containerName = 'container';
        $content = 'Content 1';
        $contentOne = 'Content 2';
        $containerContent = 'Content in container';

        $parent = $this->_block->getNameInLayout();
        $this->_layout->addBlock('Magento\View\Element\Text', $blockName, $parent)->setText($content);
        $this->_layout->addContainer($containerName, 'Container', array(), $parent);
        $this->_layout->addBlock('Magento\View\Element\Text', '', $containerName)->setText($containerContent);
        $this->_layout->addBlock('Magento\View\Element\Text', $blockNameOne, $parent)->setText($contentOne);

        $result = $this->_block->toHtml();
        $expectedDecoded = array(
            $blockName => $content,
            $containerName => $containerContent,
            $blockNameOne => $contentOne
        );
        $this->assertEquals(
            $expectedDecoded,
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Core\Helper\Data'
            )->jsonDecode(
                $result
            )
        );
    }
}
