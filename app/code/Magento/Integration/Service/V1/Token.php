<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service\V1;

use Magento\Framework\Exception\InputException;
use \Magento\Integration\Model\Oauth\Token as TokenModel;

class Token implements TokenInterface
{

    /**
     * Token Model
     *
     * @var TokenModel
     */
    private $token;

    /**
     * Initialize service
     *
     * @param TokenModel $token
     */
    public function __construct(TokenModel $token)
    {
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function createAdminAccessToken($userId)
    {
        if (!$userId) {
            throw InputException::requiredField('userId');
        }
        return $this->token->createAdminToken($userId);
    }

    /**
     * {@inheritdoc}
     */
    public function createCustomerAccessToken($userId)
    {
        if (!$userId) {
            throw InputException::requiredField('userId');
        }
        return $this->token->createCustomerToken($userId);
    }
}
