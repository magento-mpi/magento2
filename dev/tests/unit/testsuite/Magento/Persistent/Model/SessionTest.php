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

    /**
     * @var \Magento\Core\Model\Resource\AbstractResource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_configMock = $this->getMock('Magento\Session\Config\ConfigInterface');
        $this->_cookieMock = $this->getMock('Magento\Stdlib\Cookie', array(), array(), '', false);
        $this->_resource = $this->getMockForAbstractClass('Magento\Core\Model\Resource\Db\AbstractDb',
            array(), '', false, false, true,
            array('__wakeup', 'getIdFieldName', 'getConnection', 'beginTransaction', 'delete', 'commit', 'rollBack'));
        $this->_model = $helper->getObject('Magento\Persistent\Model\Session', array(
            'sessionConfig' => $this->_configMock,
            'cookie'        => $this->_cookieMock,
            'resource'      => $this->_resource
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
        $this->_cookieMock->expects($this->once())->method('set')->with(
            \Magento\Persistent\Model\Session::COOKIE_NAME, $this->anything(), $this->anything(), $cookiePath
        );
        $this->_model->delete();
    }
}
