<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Customer\Model\Authorization;

use Magento\Authorization\Model\UserContextInterface;

/**
 * Tests Magento\Customer\Model\Authorization\CustomerSessionUserContext
 */
class CustomerSessionUserContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Customer\Model\Authorization\CustomerSessionUserContext
     */
    protected $customerSessionUserContext;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->customerSession = $this->getMockBuilder('Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();

        $this->customerSessionUserContext = $this->objectManager->getObject(
            'Magento\Customer\Model\Authorization\CustomerSessionUserContext',
            ['customerSession' => $this->customerSession]
        );
    }

    public function testGetUserIdExist()
    {
        $userId = 1;
        $this->setupUserId($userId);
        $this->assertEquals($userId, $this->customerSessionUserContext->getUserId());
    }

    public function testGetUserIdDoesNotExist()
    {
        $userId = null;
        $this->setupUserId($userId);
        $this->assertEquals($userId, $this->customerSessionUserContext->getUserId());
    }

    public function testGetUserType()
    {
        $this->assertEquals(UserContextInterface::USER_TYPE_CUSTOMER, $this->customerSessionUserContext->getUserType());
    }

    /**
     * @param int|null $userId
     * @return void
     */
    public function setupUserId($userId)
    {
        $this->customerSession->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($userId));
    }
}
