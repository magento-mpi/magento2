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

class ButtonsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * VDE toolbar buttons block
     *
     * @var \Magento\DesignEditor\Block\Adminhtml\Editor\Toolbar\Buttons
     */
    protected $_block;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $_urlBuilder;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_urlBuilder = $this->getMock('Magento\Backend\Model\Url', array('getUrl'), array(), '', false);

        $arguments = array(
            'urlBuilder' => $this->_urlBuilder
        );

        $this->_block = $helper->getObject('Magento\DesignEditor\Block\Adminhtml\Editor\Toolbar\Buttons', $arguments);
    }

    public function testGetThemeId()
    {
        $this->_block->setThemeId(1);
        $this->assertEquals(1, $this->_block->getThemeId());
    }

    public function testSetThemeId()
    {
        $this->_block->setThemeId(2);
        $this->assertAttributeEquals(2, '_themeId', $this->_block);
    }
}
