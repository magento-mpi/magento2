<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Model;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Persistent\Model\Session
     */
    protected $_model;

    /**
     * @var \Magento\Session\Config\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var \Magento\Stdlib\Cookie|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cookieMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_configMock = $this->getMock('Magento\Session\Config\ConfigInterface');
        $this->_cookieMock = $this->getMock('Magento\Stdlib\Cookie', array(), array(), '', false);
        $resourceMock = $this->getMockForAbstractClass('Magento\Framework\Model\Resource\Db\AbstractDb',
            array(), '', false, false, true,
            array('__wakeup', 'getIdFieldName', 'getConnection', 'beginTransaction', 'delete', 'commit', 'rollBack'));

        $appStateMock = $this->getMock('Magento\Framework\App\State', array(), array(), '', false);
        $eventDispatcherMock = $this->getMock('Magento\Framework\Event\ManagerInterface', array(), array(), '', false, false);
        $cacheManagerMock = $this->getMock('Magento\Framework\App\CacheInterface', array(), array(), '', false, false);
        $loggerMock = $this->getMock('Magento\Logger', array(), array(), '', false);
        $actionValidatorMock = $this->getMock(
            '\Magento\Framework\Model\ActionValidator\RemoveAction', array(), array(), '', false
        );
        $actionValidatorMock->expects($this->any())->method('isAllowed')->will($this->returnValue(true));

        $context = new \Magento\Framework\Model\Context(
            $loggerMock, $eventDispatcherMock, $cacheManagerMock, $appStateMock, $actionValidatorMock
        );

        $this->_model = $helper->getObject('Magento\Persistent\Model\Session', array(
            'sessionConfig' => $this->_configMock,
            'cookie'        => $this->_cookieMock,
            'resource'      => $resourceMock,
            'context'       => $context
        ));
    }

    /**
     * @covers \Magento\Persistent\Model\Session::_afterDeleteCommit
     * @covers \Magento\Persistent\Model\Session::removePersistentCookie
     */
    public function testAfterDeleteCommit()
    {
        $cookiePath = 'some_path';
        $this->_configMock->expects($this->once())->method('getCookiePath')->will($this->returnValue($cookiePath));
        $this->_cookieMock->expects(
            $this->once()
        )->method(
            'set'
        )->with(
            \Magento\Persistent\Model\Session::COOKIE_NAME,
            $this->anything(),
            $this->anything(),
            $cookiePath
        );
        $this->_model->delete();
    }
}
