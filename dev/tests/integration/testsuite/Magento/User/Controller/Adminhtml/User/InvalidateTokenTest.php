<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Controller\Adminhtml\User;

use Magento\Framework\Message\MessageInterface;

/**
 * Test class for Magento\User\Controller\Adminhtml\User\InvalidateToken.
 */
class InvalidateTokenTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testInvalidateToken()
    {
        $tokenService = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Integration\Service\V1\TokenService');
        $tokenModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Integration\Model\Oauth\Token');
        $userModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\User\Model\User');

        $adminUserNameFromFixture = 'adminUser';
        $accessToken = $tokenService->createAdminAccessToken(
            $adminUserNameFromFixture,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );
        $adminUserId = $userModel->loadByUsername($adminUserNameFromFixture)->getId();
        /** @var $token TokenModel */
        $token = $tokenModel->loadByAdminId($adminUserId);
        $this->assertEquals($accessToken, $token->getToken());

        // invalidate token
        $this->getRequest()->setParam('user_id', $adminUserId);
        $this->dispatch('backend/admin/user/invalidateToken');
        $token = $tokenModel->loadByAdminId($adminUserId);
        $this->assertEquals(1, $token->getRevoked());
    }

    /**
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testInvalidateToken_NoTokens()
    {
        $userModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\User\Model\User');
        $adminUserNameFromFixture = 'adminUser';
        $adminUserId = $userModel->loadByUsername($adminUserNameFromFixture)->getId();
        // invalidate token
        $this->getRequest()->setParam('user_id', $adminUserId);
        $this->dispatch('backend/admin/user/invalidateToken');
        $this->assertSessionMessages(
            $this->equalTo(['This user has no tokens.']),
            MessageInterface::TYPE_ERROR
        );
    }

    public function testInvalidateToken_NoUser()
    {
        $this->dispatch('backend/admin/user/invalidateToken');
        $this->assertSessionMessages(
            $this->equalTo(['We can\'t find a user to revoke.']),
            MessageInterface::TYPE_ERROR
        );
    }

    public function testInvalidateToken_InvalidUser()
    {
        $adminUserId = 999;
        // invalidate token
        $this->getRequest()->setParam('user_id', $adminUserId);
        $this->dispatch('backend/admin/user/invalidateToken');
        $this->assertSessionMessages(
            $this->equalTo(['This user has no tokens.']),
            MessageInterface::TYPE_ERROR
        );
    }
}
