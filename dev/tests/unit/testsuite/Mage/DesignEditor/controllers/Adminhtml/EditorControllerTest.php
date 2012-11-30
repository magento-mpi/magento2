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
class Mage_Core_Model_Theme_EditorControllerTest extends PHPUnit_Framework_TestCase
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
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager_Zend',
            array('create', 'get'), array(), '', false);

        $this->_model = $this->getMock('Mage_DesignEditor_Adminhtml_System_Design_EditorController',
            array('_forward', '_title', '__', 'loadLayout', '_setActiveMenu', 'renderLayout'),
            array(
                $this->getMock('Zend_Controller_Request_Abstract', array(), array(), '', false),
                $this->getMock('Zend_Controller_Response_Abstract', array(), array(), '', false),
                $this->_objectManagerMock,
                $this->getMock('Mage_Core_Controller_Varien_Front', array(), array(), '', false),
                array(
                    'translator' => 'translator',
                    'helper'     => 'helper',
                    'session'    => 'session'
                )
            ));
        $this->_model->expects($this->any())->method('_title')->will($this->returnValue($this->_model));
        $this->_model->expects($this->any())->method('loadLayout');
        $this->_model->expects($this->any())->method('renderLayout');
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
            array('isPresentCustomizedThemes'), array(), '', false);
        $themeService
            ->expects($this->once())
            ->method('isPresentCustomizedThemes')
            ->will($this->returnValue($hasCustomizedThemes));
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
