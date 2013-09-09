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

class Magento_DesignEditor_Block_Adminhtml_Editor_ContainerTest extends PHPUnit_Framework_TestCase
{
    const FRAME_URL = 'controller/action';

    /**
     * Object manager helper
     *
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = new Magento_TestFramework_Helper_ObjectManager($this);
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
        $helperFactory = $this->getMock('Magento_Core_Model_Factory_Helper', array('get'));

        return array(
            'urlBuilder'    => $this->getMock('Magento_Backend_Model_Url', array(), array(), '', false),
            'helperFactory' => $helperFactory
        );
    }

    /**
     * @covers Magento_DesignEditor_Block_Adminhtml_Editor_Container::setFrameUrl
     * @covers Magento_DesignEditor_Block_Adminhtml_Editor_Container::getFrameUrl
     */
    public function testGetSetFrameUrl()
    {
        $arguments = array(
            'urlBuilder' => $this->getMock('Magento_Backend_Model_Url', array(), array(), '', false),
        );

        /** @var $block Magento_DesignEditor_Block_Adminhtml_Editor_Container */
        $block = $this->_helper->getObject('Magento_DesignEditor_Block_Adminhtml_Editor_Container', $arguments);
        $block->setFrameUrl(self::FRAME_URL);
        $this->assertAttributeEquals(self::FRAME_URL, '_frameUrl', $block);
        $this->assertEquals(self::FRAME_URL, $block->getFrameUrl());
    }

    /**
     * @covers Magento_DesignEditor_Block_Adminhtml_Editor_Container::_prepareLayout
     */
    public function testPrepareLayout()
    {
        $buttonTitle = 'Back';
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $arguments = $this->_getBlockArguments();
        $arguments['eventManager'] = $eventManager;

        /** @var $block Magento_DesignEditor_Block_Adminhtml_Editor_Container */
        $block = $this->_helper->getObject('Magento_DesignEditor_Block_Adminhtml_Editor_Container', $arguments);

        $layout = $this->getMock('Magento_Core_Model_Layout', array(), array(), '', false);
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
