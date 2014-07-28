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
 * Tests \Magento\Webapi\Model\Authorization\OauthUserContext
 */
class OauthUserContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Webapi\Model\Authorization\OauthUserContext
     */
    protected $oauthUserContext;

    /**
     * @var \Magento\Webapi\Controller\Request
     */
    protected $request;

    /**
     * @var \Magento\Integration\Model\Integration\Factory
     */
    protected $integrationFactory;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->request = $this->getMockBuilder('Magento\Webapi\Controller\Request')
            ->disableOriginalConstructor()
            ->setMethods(['getConsumerId'])
            ->getMock();

        $this->integrationFactory = $this->getMockBuilder('Magento\Integration\Model\Integration\Factory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->oauthUserContext = $this->objectManager->getObject(
            'Magento\Webapi\Model\Authorization\OauthUserContext',
            [
                'request' => $this->request,
                'integrationFactory' => $this->integrationFactory,
            ]
        );
    }

    public function testGetUserType()
    {
        $this->assertEquals(UserIdentifier::USER_TYPE_INTEGRATION, $this->oauthUserContext->getUserType());
    }

    public function testGetUserIdExist()
    {
        $integrationId = 12345;

        $this->setupUserId($integrationId);

        $this->assertEquals($integrationId, $this->oauthUserContext->getUserId());
    }

    public function testGetUserIdDoesNotExist()
    {
        $integrationId = null;

        $this->setupUserId($integrationId);

        $this->assertEquals($integrationId, $this->oauthUserContext->getUserId());
    }

    /**
     * @param int|null $integrationId
     * @return void
     */
    public function setupUserId($integrationId)
    {
        $consumerId = 'consumerId123';

        $this->request->expects($this->once())
            ->method('getConsumerId')
            ->will($this->returnValue($consumerId));

        $integration = $this->getMockBuilder('Magento\Integration\Model\Integration')
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'loadByConsumerId', '__wakeup'])
            ->getMock();
        $this->integrationFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($integration));
        $integration->expects($this->once())
            ->method('loadByConsumerId')
            ->with($consumerId)
            ->will($this->returnSelf());

        $expectsIdCall = 1;
        if ($integrationId) {
            $expectsIdCall = 2;
        }
        $integration->expects($this->exactly($expectsIdCall))
            ->method('getId')
            ->will($this->returnValue($integrationId));
    }
}
