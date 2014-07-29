<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Model\Authorization;

use Magento\Authz\Model\UserIdentifier;

/**
 * Tests Magento\User\Model\Authorization\GuestUserContext
 */
class GuestUserContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\User\Model\Authorization\GuestUserContext
     */
    protected $guestUserContext;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->guestUserContext = $this->objectManager->getObject(
            'Magento\User\Model\Authorization\GuestUserContext'
        );
    }

    public function testGetUserId()
    {
        $this->assertEquals(null, $this->guestUserContext->getUserId());
    }

    public function testGetUserType()
    {
        $this->assertEquals(UserIdentifier::USER_TYPE_GUEST, $this->guestUserContext->getUserType());
    }
}
