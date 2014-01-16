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

namespace Magento\DesignEditor\Block\Adminhtml\Editor;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    const FRAME_URL = 'controller/action';

    /**
     * Object manager helper
     *
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    protected function tearDown()
    {
        unset($this->_helper);
    }

    /**
     * Retrieve list of arguments for block that will be tested
     *
     * @return array
     */
    protected function _getBlockArguments()
    {
        return array(
            'urlBuilder'    => $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false),
        );
    }

    /**
     * @covers \Magento\DesignEditor\Block\Adminhtml\Editor\Container::setFrameUrl
     * @covers \Magento\DesignEditor\Block\Adminhtml\Editor\Container::getFrameUrl
     */
    public function testGetSetFrameUrl()
    {
        $arguments = array(
            'urlBuilder' => $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false),
        );

        /** @var $block \Magento\DesignEditor\Block\Adminhtml\Editor\Container */
        $block = $this->_helper->getObject('Magento\DesignEditor\Block\Adminhtml\Editor\Container', $arguments);
        $block->setFrameUrl(self::FRAME_URL);
        $this->assertAttributeEquals(self::FRAME_URL, '_frameUrl', $block);
        $this->assertEquals(self::FRAME_URL, $block->getFrameUrl());
    }

    /**
     * @covers \Magento\DesignEditor\Block\Adminhtml\Editor\Container::_prepareLayout
     */
    public function testPrepareLayout()
    {
        $buttonTitle = 'Back';
        $eventManager = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);
        $arguments = $this->_getBlockArguments();
        $arguments['eventManager'] = $eventManager;

        /** @var $block \Magento\DesignEditor\Block\Adminhtml\Editor\Container */
        $block = $this->_helper->getObject('Magento\DesignEditor\Block\Adminhtml\Editor\Container', $arguments);

        $layout = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $block->setLayout($layout);

        $expectedButtonData = array(
            'back_button'    => array(
                'label'      => $buttonTitle,
                'onclick'    => 'setLocation(\'\')',
                'class'      => 'back',
                'id'         => 'back_button',
                'region'     => 'header',
                'sort_order' => 10
            )
        );

        $this->assertAttributeContains($expectedButtonData, '_buttons', $block);
    }
}
