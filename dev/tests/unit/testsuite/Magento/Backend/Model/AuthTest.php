<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model;

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class AuthTest
 * @package Magento\Backend\Model
 */
class AuthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_credentialStorage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelFactoryMock;

    protected function setUp()
    {
        $this->_eventManagerMock = $this->getMock('\Magento\Event\ManagerInterface');
        $this->_credentialStorage = $this->getMock('\Magento\Backend\Model\Auth\Credential\StorageInterface');
        $this->_modelFactoryMock = $this->getMock('\Magento\Core\Model\Factory', array(), array(), '', false);
        $objectManager= new ObjectManager($this);
        $this->_model = $objectManager->getObject(
            'Magento\Backend\Model\Auth',
            [
                'eventManager' => $this->_eventManagerMock,
                'credentialStorage' => $this->_credentialStorage,
                'modelFactory' => $this->_modelFactoryMock
            ]
        );
    }

    /**
     * @expectedException \Magento\Backend\Model\Auth\Exception
     * @expectedExceptionMessage Please correct the user name or password.
     */
    public function testLoginFailed()
    {
        $this->_modelFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with('Magento\Backend\Model\Auth\Credential\StorageInterface')
            ->will($this->returnValue($this->_credentialStorage));
        $exceptionMock = new \Magento\Model\Exception;
        $this->_credentialStorage
            ->expects($this->once())
            ->method('login')
            ->with('username', 'password')
            ->will($this->throwException($exceptionMock));
        $this->_credentialStorage->expects($this->never())->method('getId');
        $this->_eventManagerMock->expects($this->once())->method('dispatch')->with('backend_auth_user_login_failed');
        $this->_model->login('username', 'password');
    }
}
