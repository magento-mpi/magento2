<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_ContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Container
     */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();

        $this->_block = Mage::app()->getLayout()->createBlock(
            '\Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Container'
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
