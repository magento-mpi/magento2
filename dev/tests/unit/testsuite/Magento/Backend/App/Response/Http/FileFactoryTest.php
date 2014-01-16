<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App\Response\Http;

class FileFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_backendUrl;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_responseMock = $this->getMock('Magento\App\Response\Http', array('setRedirect'), array(), '', false);
        $this->_responseMock
            ->expects($this->any())
            ->method('setRedirect')
            ->will($this->returnValue($this->_responseMock));
        $this->_sessionMock =
            $this->getMock('Magento\Backend\Model\Session', array('setIsUrlNotice'), array(), '', false);
        $this->_backendUrl = $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false);
        $this->_authMock = $this->getMock('Magento\Backend\Model\Auth', array(), array(), '', false);
        $this->_model = $helper->getObject('Magento\Backend\App\Response\Http\FileFactory', array(
                'response' => $this->_responseMock,
                'auth' => $this->_authMock,
                'backendUrl' => $this->_backendUrl,
                'session' => $this->_sessionMock,
            )
        );
    }

    public function testCreate()
    {
        $authStorageMock = $this->getMock(
            'Magento\Backend\Model\Auth\StorageInterface',
            array('isFirstPageAfterLogin', 'processLogout', 'processLogin ')
        );
        $this->_authMock->expects($this->once())->method('getAuthStorage')->will($this->returnValue($authStorageMock));
        $authStorageMock->expects($this->once())->method('isFirstPageAfterLogin')->will($this->returnValue(true));
        $this->_sessionMock->expects($this->once())->method('setIsUrlNotice');
        $this->_model->create('fileName', null);
    }
}
