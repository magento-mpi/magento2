<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Block\Customer\Wishlist\Item;

class ColumnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout = null;

    /**
     * @var \Magento\Wishlist\Block\Customer\Wishlist\Item\Column
     */
    protected $_block = null;

    protected function setUp()
    {
        $this->_layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        $this->_block = $this->_layout->addBlock('Magento\Wishlist\Block\Customer\Wishlist\Item\Column', 'test');
        $this->_layout->addBlock('Magento\Core\Block\Text', 'child', 'test');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        $item = new \StdClass;
        $this->_block->setItem($item);
        $this->_block->toHtml();
        $this->assertSame($item, $this->_layout->getBlock('child')->getItem());
    }

    public function testGetJs()
    {
        $expected = uniqid();
        $this->_layout->getBlock('child')->setJs($expected);
        $this->assertEquals($expected, $this->_block->getJs());
    }
}
