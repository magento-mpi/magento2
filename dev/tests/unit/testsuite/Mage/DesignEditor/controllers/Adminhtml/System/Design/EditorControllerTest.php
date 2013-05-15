<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'Mage/DesignEditor/controllers/Adminhtml/System/Design/EditorController.php';
/**
 * Test backend controller for the design editor
 */
class Mage_DesignEditor_Controller_Adminhtml_System_Design_EditorControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Adminhtml_System_Design_EditorController
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $arguments = array(
            'objectManager' => $this->_objectManagerMock
        );
        $context = $helper->getObject('Mage_Backend_Controller_Context', $arguments);

        $this->_model = $this->getMock('Mage_DesignEditor_Adminhtml_System_Design_EditorController',
            array('_forward', '_title', '__', 'loadLayout', '_setActiveMenu', 'renderLayout', 'getLayout'),
            array(
                $context,
                $this->getMock('Mage_DesignEditor_Model_Theme_Context', array(), array(), '', false),
                null
            ));
        /** @var $layoutMock Mage_Core_Model_Layout|PHPUnit_Framework_MockObject_MockObject */
        $layoutMock  = $this->getMock('Mage_Core_Model_Layout', array('getBlock'), array(), '', false);
        /** @var $layoutMock Mage_Core_Model_Layout */
        $storeViewMock  = $this->getMock('Mage_DesignEditor_Block_Adminhtml_Theme_Selector_StoreView',
            array('setData'), array(), '', false);
        $layoutMock->expects($this->any())->method('getBlock')->will($this->returnValue($storeViewMock));

        $this->_model->expects($this->any())->method('_title')->will($this->returnValue($this->_model));
        $this->_model->expects($this->any())->method('loadLayout');
        $this->_model->expects($this->any())->method('renderLayout');
        $this->_model->expects($this->any())->method('getLayout')->will($this->returnValue($layoutMock));
        $this->_model->expects($this->any())->method('_setActiveMenu');
        $this->_model->expects($this->any())->method('__');
    }

    /**
     * Return mocked theme service model
     *
     * @param  bool $hasCustomizedThemes
     * @return Mage_Core_Model_Theme_Service
     */
    protected function _getThemeService($hasCustomizedThemes)
    {
        $themeService = $this->getMock('Mage_Core_Model_Theme_Service',
            array('isCustomizationsExist'), array(), '', false);
        $themeService
            ->expects($this->at(0))
            ->method('isCustomizationsExist')
            ->will($this->returnValue($hasCustomizedThemes));

        $themeService
            ->expects($this->any())
            ->method('isCustomizationsExist')
            ->will($this->returnValue(false));
        return $themeService;
    }

    /**
     * @covers Mage_DesignEditor_Adminhtml_System_Design_EditorController::indexAction
     * @dataProvider indexActionDataProvider
     */
    public function testIndexAction($hasCustomizedThemes, $forwardCalls)
    {
        $this->_objectManagerMock
            ->expects($this->any())
            ->method('get')
            ->with('Mage_Core_Model_Theme_Service')
            ->will($this->returnValue($this->_getThemeService($hasCustomizedThemes)));

        $this->_model
            ->expects($this->any())
            ->method('_doSelectionTheme')
            ->with('firstEntrance');

        $this->_model
            ->expects($this->exactly($forwardCalls))
            ->method('_forward');

        $this->_model->indexAction();
    }

    /**
     * @return array
     */
    public function indexActionDataProvider()
    {
        return array(
            array(true, 0),
            array(false, 1)
        );
    }

    /**
     * @covers Mage_DesignEditor_Adminhtml_System_Design_EditorController::firstEntranceAction
     * @dataProvider firstEntranceActionDataProvider
     */
    public function testFirstEntranceAction($hasCustomizedThemes, $forwardCalls)
    {
        $this->_objectManagerMock
            ->expects($this->any())
            ->method('get')
            ->with('Mage_Core_Model_Theme_Service')
            ->will($this->returnValue($this->_getThemeService($hasCustomizedThemes)));

        $this->_model
            ->expects($this->any())
            ->method('_doSelectionTheme')
            ->with('index');

        $this->_model
            ->expects($this->exactly($forwardCalls))
            ->method('_forward');

        $this->_model->firstEntranceAction();
    }

    /**
     * @return array
     */
    public function firstEntranceActionDataProvider()
    {
        return array(
            array(true, 1),
            array(false, 0)
        );
    }

}
