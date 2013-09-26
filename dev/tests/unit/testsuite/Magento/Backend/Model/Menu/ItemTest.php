<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Menu_ItemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Menu_Item
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_aclMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_validatorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleListMock;

    /**
     * @var array
     */
    protected $_params = array(
        'id' => 'item',
        'title' => 'Item Title',
        'action' => '/system/config',
        'resource' => 'Magento_Backend::config',
        'dependsOnModule' => 'Magento_Backend',
        'dependsOnConfig' => 'system/config/isEnabled',
        'tooltip' => 'Item tooltip',
    );

    protected function setUp()
    {
        $this->_aclMock = $this->getMock('Magento_AuthorizationInterface');
        $this->_storeConfigMock = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        $this->_menuFactoryMock = $this
            ->getMock('Magento_Backend_Model_MenuFactory', array('create'), array(), '', false);
        $this->_urlModelMock = $this->getMock('Magento_Backend_Model_Url', array(), array(), '', false);
        $this->_helperMock = $this->getMock('Magento_Backend_Helper_Data', array(), array(), '', false);
        $this->_validatorMock = $this->getMock('Magento_Backend_Model_Menu_Item_Validator');
        $this->_validatorMock->expects($this->any())
            ->method('validate');
        $this->_moduleListMock = $this->getMock('Magento_Core_Model_ModuleListInterface');

        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_model = $helper->getObject('Magento_Backend_Model_Menu_Item', array(
            'validator'     => $this->_validatorMock,
            'authorization' => $this->_aclMock,
            'storeConfig'   => $this->_storeConfigMock,
            'menuFactory'   => $this->_menuFactoryMock,
            'urlModel'      => $this->_urlModelMock,
            'helper'        => $this->_helperMock,
            'moduleList'    => $this->_moduleListMock,
            'data'          => $this->_params
        ));
    }

    public function testGetUrlWithEmptyActionReturnsHashSign()
    {
        $this->_params['action'] = '';
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $item = $helper->getObject('Magento_Backend_Model_Menu_Item', array(
            'menuFactory' => $this->_menuFactoryMock,
            'data'        => $this->_params
        ));
        $this->assertEquals('#', $item->getUrl());
    }

    public function testGetUrlWithValidActionReturnsUrl()
    {
        $this->_urlModelMock->expects($this->once())
            ->method('getUrl')
            ->with(
                $this->equalTo('/system/config')
            )
            ->will($this->returnValue('Url'));
        $this->assertEquals('Url', $this->_model->getUrl());
    }

    public function testHasClickCallbackReturnsFalseIfItemHasAction()
    {
        $this->assertFalse($this->_model->hasClickCallback());
    }

    public function testHasClickCallbackReturnsTrueIfItemHasNoAction()
    {
        $this->_params['action'] = '';
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $item = $helper->getObject('Magento_Backend_Model_Menu_Item', array(
            'menuFactory' => $this->_menuFactoryMock,
            'data'        => $this->_params
        ));
        $this->assertTrue($item->hasClickCallback());
    }

    public function testGetClickCallbackReturnsStoppingJsIfItemDoesntHaveAction()
    {
        $this->_params['action'] = '';
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $item = $helper->getObject('Magento_Backend_Model_Menu_Item', array(
            'menuFactory' => $this->_menuFactoryMock,
            'data'        => $this->_params
        ));
        $this->assertEquals('return false;', $item->getClickCallback());
    }

    public function testGetClickCallbackReturnsEmptyStringIfItemHasAction()
    {
        $this->assertEquals('', $this->_model->getClickCallback());
    }

    public function testIsDisabledReturnsTrueIfModuleOutputIsDisabled()
    {
        $this->_helperMock->expects($this->once())
            ->method('isModuleOutputEnabled')
            ->will($this->returnValue(false));
        $this->assertTrue($this->_model->isDisabled());
    }

    public function testIsDisabledReturnsTrueIfModuleDependenciesFail()
    {
        $this->_helperMock->expects($this->once())
            ->method('isModuleOutputEnabled')
            ->will($this->returnValue(true));

        $this->_moduleListMock->expects($this->once())
            ->method('getModule')
            ->will($this->returnValue(array('name' => 'Magento_Backend')));

        $this->assertTrue($this->_model->isDisabled());
    }

    public function testIsDisabledReturnsTrueIfConfigDependenciesFail()
    {
        $this->_helperMock->expects($this->once())
            ->method('isModuleOutputEnabled')
            ->will($this->returnValue(true));

        $this->_moduleListMock->expects($this->once())
            ->method('getModule')
            ->will($this->returnValue(array('name' => 'Magento_Backend')));

        $this->assertTrue($this->_model->isDisabled());
    }

    public function testIsDisabledReturnsFalseIfNoDependenciesFail()
    {
        $this->_helperMock->expects($this->once())
            ->method('isModuleOutputEnabled')
            ->will($this->returnValue(true));

        $this->_moduleListMock->expects($this->once())
            ->method('getModule')
            ->will($this->returnValue(array('name' => 'Magento_Backend')));

        $this->_storeConfigMock->expects($this->once())
            ->method('getConfigFlag')
            ->will($this->returnValue(true));

        $this->assertFalse($this->_model->isDisabled());
    }

    public function testIsAllowedReturnsTrueIfResourceIsAvailable()
    {
        $this->_aclMock->expects($this->once())
            ->method('isAllowed')
            ->with('Magento_Backend::config')
            ->will($this->returnValue(true));
        $this->assertTrue($this->_model->isAllowed());
    }

    public function testIsAllowedReturnsFalseIfResourceIsNotAvailable()
    {
        $this->_aclMock->expects($this->once())
            ->method('isAllowed')
            ->with('Magento_Backend::config')
            ->will($this->throwException(new Magento_Exception()));
        $this->assertFalse($this->_model->isAllowed());
    }

    public function testGetChildrenCreatesSubmenuOnFirstCall()
    {
        $menuMock = $this->getMock('Magento_Backend_Model_Menu', array(), array(), '', false);

        $this->_menuFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($menuMock));

        $this->_model->getChildren();
        $this->_model->getChildren();
    }
}

class Magento_Test_Module_Config
{
    /**
     *
     * @SuppressWarnings(PHPMD.ShortMethodName))
     */
    public function is()
    {

    }
}
