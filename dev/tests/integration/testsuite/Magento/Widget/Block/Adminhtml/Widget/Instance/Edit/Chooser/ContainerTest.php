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
    protected $block = null;

    protected function setUp()
    {
        parent::setUp();

        $this->block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        )->createBlock(
            'Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Container'
        );
    }

    public function testSetGetAllowedContainers()
    {
        $this->assertEmpty($this->block->getAllowedContainers());
        $containers = array('some_container', 'another_container');
        $this->block->setAllowedContainers($containers);
        $this->assertEquals($containers, $this->block->getAllowedContainers());
    }

    /**
     * Test verify that theme contains available containers for widget
     */
    public function testAvailableContainers()
    {
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\DesignInterface'
        );
        $this->block->setTheme($design->getDesignTheme()->getId());
        $this->assertContains('<option value="before.body.end" >', $this->block->toHtml());
    }
}
