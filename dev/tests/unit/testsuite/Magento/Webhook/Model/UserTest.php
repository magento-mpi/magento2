<?php
/**
 * \Magento\Webhook\Model\User
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webhook\Model\User */
    protected $_user;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockAclUser;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_mockAuthorization;

    protected function setUp()
    {
        $webApiId = 'web api id';

        $this->_mockAclUser = $this->getMockBuilder('Magento\Webapi\Model\Acl\User\Factory')
            ->setMethods(array('load', 'getRoleId', 'getSecret'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockUserFactory = $this->getMockBuilder('Magento\Webapi\Model\Acl\User\Factory')
            ->setMethods(array('create'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockUserFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_mockAclUser));

        $this->_mockAclUser->expects($this->once())
            ->method('load')
            ->with($this->equalTo($webApiId));

        $mockRLocatorFactory = $this->getMockBuilder('Magento\Webapi\Model\Authorization\Role\Locator\Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_mockAclUser->expects($this->once())
            ->method('getRoleId')
            ->will($this->returnValue('role_id'));

        $mockRLocatorFactory->expects($this->once())
            ->method('create')
            ->with(array('data' => array('roleId' => 'role_id')))
            ->will($this->returnValue('role_locator'));

        $this->_mockAuthorization = $this->getMockBuilder('Magento\Authorization')
            ->setMethods(array('isAllowed'))
            ->disableOriginalConstructor()
            ->getMock();

        $mockAclPolicy = $this->getMockBuilder('Magento\Webapi\Model\Authorization\Policy\Acl')
            ->disableOriginalConstructor()
            ->getMock();

        $mockAuthFactory = $this->getMockBuilder('Magento\Authorization\Factory')
            ->disableOriginalConstructor()
            ->getMock();

        $mockAuthFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_mockAuthorization));

        $this->_user = new \Magento\Webhook\Model\User(
            $mockUserFactory,
            $mockRLocatorFactory,
            $mockAclPolicy,
            $mockAuthFactory,
            $webApiId
        );
    }

    public function testGetSharedSecret()
    {
        $sharedSecret = 'some random shared secret';

        $this->_mockAclUser->expects($this->once())
            ->method('getSecret')
            ->will($this->returnValue($sharedSecret));

        $this->assertSame($sharedSecret, $this->_user->getSharedSecret());
    }

    public function testHasPermission()
    {
        $allowedTopic = 'allowed topic';
        $notAllowedTopic = 'not allowed topic';

        $this->_mockAuthorization->expects($this->any())
            ->method('isAllowed')
            ->will(
                $this->returnValueMap(
                    array(
                         array($allowedTopic, null, true),
                         array($notAllowedTopic, null, false)
                    )
                )
            );

        $this->assertTrue($this->_user->hasPermission($allowedTopic));
        $this->assertFalse($this->_user->hasPermission($notAllowedTopic));
    }
}
