<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Toolbar;

class BlockAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * VDE toolbar buttons block
     *
     * @var \Magento\DesignEditor\Block\Adminhtml\Editor\Toolbar\AbstractBlock
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = $this->getMockForAbstractClass(
            'Magento\DesignEditor\Block\Adminhtml\Editor\Toolbar\AbstractBlock',
            array(),
            '',
            false
        );
    }

    public function testGetMode()
    {
        $this->_block->setMode(\Magento\DesignEditor\Model\State::MODE_NAVIGATION);
        $this->assertEquals(\Magento\DesignEditor\Model\State::MODE_NAVIGATION, $this->_block->getMode());
    }

    public function testSetMode()
    {
        $this->_block->setMode(\Magento\DesignEditor\Model\State::MODE_NAVIGATION);
        $this->assertAttributeEquals(\Magento\DesignEditor\Model\State::MODE_NAVIGATION, '_mode', $this->_block);
    }

    public function testIsNavigationMode()
    {
        $this->_block->setMode(\Magento\DesignEditor\Model\State::MODE_NAVIGATION);
        $this->assertTrue($this->_block->isNavigationMode());

        $this->_block->setMode('model_that_is_not_exist');
        $this->assertFalse($this->_block->isNavigationMode());
    }
}
