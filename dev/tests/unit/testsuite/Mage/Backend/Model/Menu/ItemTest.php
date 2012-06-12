<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Menu_ItemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu_Item
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_aclMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appConfigMock;

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
     * @var array
     */
    protected $_params = array(
        'id' => 'item',
        'title' => 'Item Title',
        'action' => '/system/config',
        'resource' => 'system/config',
        'dependsOnModule' => 'Mage_Backend',
        'dependsOnConfig' => 'system/config/isEnabled',
        'tooltip' => 'Item tooltip',
    );

    public function setUp()
    {
        $this->_aclMock = $this->getMock('Mage_Backend_Model_Auth_Session');
        $this->_appConfigMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_storeConfigMock = $this->getMock('Mage_Core_Model_Store_Config');
        $this->_objectFactoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_urlModelMock = $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false);
        $this->_helperMock = $this->getMock('Mage_Backend_Helper_Data');
        $this->_validatorMock = $this->getMock('Mage_Backend_Model_Menu_Item_Validator');

        $this->_params['module'] = $this->_helperMock;
        $this->_params['acl'] = $this->_aclMock;
        $this->_params['appConfig'] = $this->_appConfigMock;
        $this->_params['storeConfig'] = $this->_storeConfigMock;
        $this->_params['objectFactory'] = $this->_objectFactoryMock;
        $this->_params['urlModel'] = $this->_urlModelMock;
        $this->_params['validator'] = $this->_validatorMock;
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorRequiresValidator()
    {
        unset($this->_params['validator']);
        new Mage_Backend_Model_Menu_Item($this->_params);
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testConstructorValidatesData()
    {
        $this->_validatorMock->expects($this->once())
            ->method('validate')
            ->will($this->throwException(new BadMethodCallException()));
        new Mage_Backend_Model_Menu_Item($this->_params);
    }

    public function testGetFullPathReturnsPathWithItemId()
    {
        $item = new Mage_Backend_Model_Menu_Item($this->_params);
        $this->assertEquals('item', $item->getFullPath());
    }

    public function testGetUrlWithEmptyActionReturnsHashSign()
    {
        $this->_params['action'] = '';
        $item = new Mage_Backend_Model_Menu_Item($this->_params);
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
        $item = new Mage_Backend_Model_Menu_Item($this->_params);
        $this->assertEquals('Url', $item->getUrl());
    }

    public function testHasClickCallbackReturnsFalseIfItemHasAction()
    {
        $item = new Mage_Backend_Model_Menu_Item($this->_params);
        $this->assertFalse($item->hasClickCallback());
    }

    public function testHasClickCallbackReturnsTrueIfItemHasNoAction()
    {
        $this->_params['action'] = '';
        $item = new Mage_Backend_Model_Menu_Item($this->_params);
        $this->assertTrue($item->hasClickCallback());
    }

    public function testGetClickCallbackReturnsStoppingJsIfItemDoesntHaveAction()
    {
        $this->_params['action'] = '';
        $item = new Mage_Backend_Model_Menu_Item($this->_params);
        $this->assertEquals('return false;', $item->getClickCallback());
    }

    public function testGetClickCallbackReturnsEmptyStringIfItemHasAction()
    {
        $item = new Mage_Backend_Model_Menu_Item($this->_params);
        $this->assertEquals('', $item->getClickCallback());
    }

    public function testIsDisabledReturnsTrueIfModuleOutputIsDisabled()
    {
        $this->_helperMock->expects($this->once())
            ->method('isModuleOutputEnabled')
            ->will($this->returnValue(false));
        $item = new Mage_Backend_Model_Menu_Item($this->_params);
        $this->assertTrue($item->isDisabled());
    }

    public function testIsDisabledReturnsTrueIfModuleDependenciesFail()
    {
        $this->_helperMock->expects($this->once())
            ->method('isModuleOutputEnabled')
            ->will($this->returnValue(true));

        $moduleConfig = new stdClass();
        $moduleConfig->{'Mage_Backend'} = $this->getMock('Mage_Test_Module_Config');
        $moduleConfig->{'Mage_Backend'}->expects($this->once())
            ->method('is')
            ->will($this->returnValue(false));

        $this->_appConfigMock->expects($this->once())
            ->method('getNode')
            ->will($this->returnValue($moduleConfig));

        $item = new Mage_Backend_Model_Menu_Item($this->_params);
        $this->assertTrue($item->isDisabled());
    }

    public function testIsDisabledReturnsTrueIfConfigDependenciesFail()
    {
        $this->_helperMock->expects($this->once())
            ->method('isModuleOutputEnabled')
            ->will($this->returnValue(true));

        $moduleConfig = new stdClass();
        $moduleConfig->{'Mage_Backend'} = $this->getMock('Mage_Test_Module_Config', array('is'));
        $moduleConfig->{'Mage_Backend'}->expects($this->once())
            ->method('is')
            ->will($this->returnValue(true));

        $this->_appConfigMock->expects($this->once())
            ->method('getNode')
            ->will($this->returnValue($moduleConfig));

        $item = new Mage_Backend_Model_Menu_Item($this->_params);
        $this->assertTrue($item->isDisabled());
    }

    public function testIsDisabledReturnsFalseIfNoDependenciesFail()
    {
        $this->_helperMock->expects($this->once())
            ->method('isModuleOutputEnabled')
            ->will($this->returnValue(true));

        $moduleConfig = new stdClass();
        $moduleConfig->{'Mage_Backend'} = $this->getMock('Mage_Test_Module_Config', array('is'));
        $moduleConfig->{'Mage_Backend'}->expects($this->once())
            ->method('is')
            ->will($this->returnValue(true));

        $this->_appConfigMock->expects($this->once())
            ->method('getNode')
            ->will($this->returnValue($moduleConfig));

        $this->_storeConfigMock->expects($this->once())
            ->method('getConfigFlag')
            ->will($this->returnValue(true));

        $item = new Mage_Backend_Model_Menu_Item($this->_params);
        $this->assertFalse($item->isDisabled());
    }

    public function testIsAllowedReturnsTrueIfResourceIsAvailable()
    {
        $this->_aclMock->expects($this->once())
            ->method('isAllowed')
            ->with('admin/system/config')
            ->will($this->returnValue(true));
        $item = new Mage_Backend_Model_Menu_Item($this->_params);
        $this->assertTrue($item->isAllowed());
    }

    public function testIsAllowedReturnsFalseIfResourceIsNotAvailable()
    {
        $this->_aclMock->expects($this->once())
            ->method('isAllowed')
            ->with('admin/system/config')
            ->will($this->throwException(new Magento_Exception()));
        $item = new Mage_Backend_Model_Menu_Item($this->_params);
        $this->assertFalse($item->isAllowed());
    }

    public function testGetChildrenCreatesSubmenuOnFirstCall()
    {
        $menuMock = $this->getMock('Mage_Backend_Model_Menu');

        $this->_objectFactoryMock->expects($this->once())
            ->method('getModelInstance')
            ->with(
                $this->equalTo('Mage_Backend_Model_Menu'),
                array(
                    'path' => 'item'
                )
            )
            ->will($this->returnValue($menuMock));
        $item = new Mage_Backend_Model_Menu_Item($this->_params);
        $item->getChildren();
    }

    public function testSetParentUpdatesAllChildren()
    {
        $menuMock = $this->getMock('Mage_Backend_Model_Menu');
        $menuMock->expects($this->once())
            ->method('setPath')
            ->with($this->equalTo('root/item'));

        $this->_objectFactoryMock->expects($this->once())
            ->method('getModelInstance')
            ->will($this->returnValue($menuMock));

        $item = new Mage_Backend_Model_Menu_Item($this->_params);
        $item->getChildren()->add($item);
        $item->setPath('root/');
    }
}

class Mage_Test_Module_Config
{
    /**
     *
     * @SuppressWarnings(PHPMD.ShortMethodName))
     */
    public function is()
    {

    }
}
