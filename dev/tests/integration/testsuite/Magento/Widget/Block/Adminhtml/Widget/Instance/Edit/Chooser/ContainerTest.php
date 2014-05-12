<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser;

/**
 * @magentoAppArea adminhtml
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Container
     */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();

        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        )->createBlock(
            'Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Container'
        );
    }

    public function testSetGetAllowedContainers()
    {
        $this->assertEmpty($this->_block->getAllowedContainers());
        $containers = array('some_container', 'another_container');
        $this->_block->setAllowedContainers($containers);
        $this->assertEquals($containers, $this->_block->getAllowedContainers());
    }
}
