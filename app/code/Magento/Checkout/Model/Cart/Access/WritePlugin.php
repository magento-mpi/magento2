<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Model\Cart\Access;

use Magento\Authorization\Model\UserContextInterface;
use \Magento\Framework\Exception\AuthorizationException;

class WritePlugin
{
    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @var int[]
     */
    protected $allowedUserTypes = [
        UserContextInterface::USER_TYPE_ADMIN,
        UserContextInterface::USER_TYPE_INTEGRATION
    ];

    /**
     * @param UserContextInterface $userContext
     */
    public function __construct(UserContextInterface $userContext)
    {
        $this->userContext = $userContext;
    }

    /**
     * Check whether access is allowed for create cart resource
     *
     * @param \Magento\Checkout\Service\V1\Cart\WriteServiceInterface $subject
     * @param int $cartId
     * @param int $customerId
     *
     * @return void
     * @throws AuthorizationException if access denied
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeAssignCustomer(
        \Magento\Checkout\Service\V1\Cart\WriteServiceInterface $subject,
        $cartId,
        $customerId
    ) {
        if (!in_array($this->userContext->getUserType(), $this->allowedUserTypes)) {
            throw new AuthorizationException('Access denied');
        }
    }
}
