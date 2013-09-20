<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account;

class NavigationTest extends \PHPUnit_Framework_TestCase
{
    public function testAddRemoveLink()
    {
        $block = \Mage::app()->getLayout()->createBlock('Magento\Customer\Block\Account\Navigation');
        $this->assertSame(array(), $block->getLinks());
        $this->assertSame($block, $block->addLink('Name', 'some/path/index', 'Label', array('parameter' => 'value')));
        $links = $block->getLinks();
        $this->assertArrayHasKey('Name', $links);
        $this->assertInstanceOf('Magento\Object', $links['Name']);
        $this->assertSame(array(
                'name' => 'Name', 'path' => 'some/path/index', 'label' => 'Label',
                'url' => 'http://localhost/index.php/some/path/index/parameter/value/'
            ), $links['Name']->getData()
        );
        $block->removeLink('nonexistent');
        $this->assertSame($links, $block->getLinks());
        $block->removeLink('Name');
        $this->assertSame(array(), $block->getLinks());
    }
}
