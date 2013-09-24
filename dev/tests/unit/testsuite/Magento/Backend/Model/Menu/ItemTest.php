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
namespace Magento\Backend\Model\Menu;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Menu\Item
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_aclMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_menuFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_validatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
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
        $this->_aclMock = $this->getMock('Magento\AuthorizationInterface');
        $this->_storeConfigMock = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);
        $this->_menuFactoryMock = $this
            ->getMock('Magento\Backend\Model\MenuFactory', array('create'), array(), '', false);
        $this->_urlModelMock = $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false);
        $this->_helperMock = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false);
        $this->_validatorMock = $this->getMock('Magento\Backend\Model\Menu\Item\Validator');
        $this->_validatorMock->expects($this->any())
            ->method('validate');
        $this->_moduleListMock = $this->getMock('Magento\Core\Model\ModuleListInterface');

        $this->_model = new \Magento\Backend\Model\Menu\Item(
            $this->_validatorMock,
            $this->_aclMock,
            $this->_storeConfigMock,
            $this->_menuFactoryMock,
            $this->_urlModelMock,
            $this->_helperMock,
            $this->_moduleListMock,
            $this->_params
        );
    }

    public function testGetUrlWithEmptyActionReturnsHashSign()
    {
        $this->_params['action'] = '';
        $item = new \Magento\Backend\Model\Menu\Item(
            $this->_validatorMock,
            $this->_aclMock,
            $this->_storeConfigMock,
            $this->_menuFactoryMock,
            $this->_urlModelMock,
            $this->_helperMock,
            $this->_moduleListMock,
            $this->_params
        );
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
        $item = new \Magento\Backend\Model\Menu\Item(
            $this->_validatorMock,
            $this->_aclMock,
            $this->_storeConfigMock,
            $this->_menuFactoryMock,
            $this->_urlModelMock,
            $this->_helperMock,
            $this->_moduleListMock,
            $this->_params
        );
        $this->assertTrue($item->hasClickCallback());
    }

    public function testGetClickCallbackReturnsStoppingJsIfItemDoesntHaveAction()
    {
        $this->_params['action'] = '';
        $item = new \Magento\Backend\Model\Menu\Item(
            $this->_validatorMock,
            $this->_aclMock,
            $this->_storeConfigMock,
            $this->_menuFactoryMock,
            $this->_urlModelMock,
            $this->_helperMock,
            $this->_moduleListMock,
            $this->_params
        );
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
            ->will($this->throwException(new \Magento\Exception()));
        $this->assertFalse($this->_model->isAllowed());
    }

    public function testGetChildrenCreatesSubmenuOnFirstCall()
    {
        $menuMock = $this->getMock('Magento\Backend\Model\Menu', array(), array(), '', false);

        $this->_menuFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($menuMock));

        $this->_model->getChildren();
        $this->_model->getChildren();
    }
}

