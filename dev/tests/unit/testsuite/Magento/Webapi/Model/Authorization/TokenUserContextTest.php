<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Authorization;

use Magento\Authz\Model\UserIdentifier;

/**
 * Tests \Magento\Webapi\Model\Authorization\TokenUserContext
 */
class TokenUserContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Webapi\Model\Authorization\TokenUserContext
     */
    protected $tokenUserContext;

    /**
     * @var \Magento\Integration\Model\Oauth\TokenFactory
     */
    protected $tokenFactory;

    /**
     * @var \Magento\Webapi\Controller\Request
     */
    protected $request;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->request = $this->getMockBuilder('Magento\Webapi\Controller\Request')
            ->disableOriginalConstructor()
            ->setMethods(['getHeader'])
            ->getMock();

        $this->tokenFactory = $this->getMockBuilder('Magento\Integration\Model\Oauth\TokenFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->tokenUserContext = $this->objectManager->getObject(
            'Magento\Webapi\Model\Authorization\TokenUserContext',
            [
                'request' => $this->request,
                'tokenFactory' => $this->tokenFactory,
            ]
        );
    }

    public function testNoAuthorizationHeader()
    {
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Authorization')
            ->will($this->returnValue(null));
        $this->assertNull($this->tokenUserContext->getUserType());
        $this->assertNull($this->tokenUserContext->getUserId());
    }

    public function testNoTokenInHeader()
    {
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Authorization')
            ->will($this->returnValue('Bearer'));
        $this->assertNull($this->tokenUserContext->getUserType());
        $this->assertNull($this->tokenUserContext->getUserId());
    }

    public function testNotBearerToken()
    {
        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Authorization')
            ->will($this->returnValue('Access'));
        $this->assertNull($this->tokenUserContext->getUserType());
        $this->assertNull($this->tokenUserContext->getUserId());
    }

    public function testNoTokenInDatabase()
    {
        $bearerToken = 'bearer1234';

        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Authorization')
            ->will($this->returnValue("Bearer {$bearerToken}"));

        $token = $this->getMockBuilder('Magento\Integration\Model\Oauth\Token')
            ->disableOriginalConstructor()
            ->setMethods(['loadByToken', 'getId', '__wakeup'])
            ->getMock();
        $this->tokenFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($token));
        $token->expects($this->once())
            ->method('loadByToken')
            ->with($bearerToken)
            ->will($this->returnSelf());
        $token->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));

        $this->assertNull($this->tokenUserContext->getUserType());
        $this->assertNull($this->tokenUserContext->getUserId());
    }

    /**
     * @dataProvider getValidTokenData
     */
    public function testValidToken($userType, $userId, $expectedUserType, $expectedUserId)
    {
        $bearerToken = 'bearer1234';

        $this->request->expects($this->once())
            ->method('getHeader')
            ->with('Authorization')
            ->will($this->returnValue("Bearer {$bearerToken}"));

        $token = $this->getMockBuilder('Magento\Integration\Model\Oauth\Token')
            ->disableOriginalConstructor()
            ->setMethods(['loadByToken', 'getId', 'getUserType', 'getCustomerId', 'getAdminId', '__wakeup'])
            ->getMock();
        $this->tokenFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($token));
        $token->expects($this->once())
            ->method('loadByToken')
            ->with($bearerToken)
            ->will($this->returnSelf());
        $token->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $token->expects($this->once())
            ->method('getUserType')
            ->will($this->returnValue($userType));
        switch($userType) {
            case UserIdentifier::USER_TYPE_ADMIN:
                $token->expects($this->once())
                    ->method('getAdminId')
                    ->will($this->returnValue($userId));
                break;
            case UserIdentifier::USER_TYPE_CUSTOMER:
                $token->expects($this->once())
                    ->method('getCustomerId')
                    ->will($this->returnValue($userId));
                break;
        }

        $this->assertEquals($expectedUserType, $this->tokenUserContext->getUserType());
        $this->assertEquals($expectedUserId, $this->tokenUserContext->getUserId());

        /* check again to make sure that the above methods were only called once */
        $this->assertEquals($expectedUserType, $this->tokenUserContext->getUserType());
        $this->assertEquals($expectedUserId, $this->tokenUserContext->getUserId());
    }

    public function getValidTokenData()
    {
        return [
            'admin token' => [
                UserIdentifier::USER_TYPE_ADMIN,
                1234,
                UserIdentifier::USER_TYPE_ADMIN,
                1234,
            ],
            'customer token' => [
                UserIdentifier::USER_TYPE_CUSTOMER,
                1234,
                UserIdentifier::USER_TYPE_CUSTOMER,
                1234,
            ],
            'integration token' => [
                UserIdentifier::USER_TYPE_INTEGRATION,
                1234,
                null,
                null,
            ],
            'guest user type' => [
                UserIdentifier::USER_TYPE_GUEST,
                1234,
                null,
                null,
            ]
        ];
    }
}
