<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Authorization;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Authz\Model\UserIdentifier;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\Webapi\Controller\Request;

/**
 * A user context determined by tokens in a HTTP request Authorization header.
 */
class TokenUserContext implements UserContextInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Token
     */
    protected $tokenFactory;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var string
     */
    protected $userType;

    /**
     * @var bool
     */
    protected $isRequestProcessed;

    /**
     * Initialize dependencies.
     *
     * @param Request $request
     * @param TokenFactory $tokenFactory
     */
    public function __construct(
        Request $request,
        TokenFactory $tokenFactory
    ) {
        $this->request = $request;
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        $this->processRequest();
        return $this->userId;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserType()
    {
        $this->processRequest();
        return $this->userType;
    }

    /**
     * Finds the bearer token and looks up the value.
     *
     * @return void
     */
    protected function processRequest()
    {
        if ($this->isRequestProcessed) {
            return;
        }

        $authorizationHeaderValue = $this->request->getHeader('Authorization');
        if (!$authorizationHeaderValue) {
            $this->isRequestProcessed = true;
            return;
        }

        $headerPieces = explode(" ", $authorizationHeaderValue);
        if (!$headerPieces || count($headerPieces) < 2) {
            $this->isRequestProcessed = true;
            return;
        }

        $tokenType = strtolower($headerPieces[0]);
        if ($tokenType !== 'bearer') {
            $this->isRequestProcessed = true;
            return;
        }

        $bearerToken = $headerPieces[1];
        $token = $this->tokenFactory->create()->loadByToken($bearerToken);

        if (!$token->getId()) {
            $this->isRequestProcessed = true;
            return;
        }

        $this->userType = $token->getUserType();
        switch ($this->userType) {
            case UserIdentifier::USER_TYPE_ADMIN:
                $this->userId = $token->getAdminId();
                break;
            case UserIdentifier::USER_TYPE_CUSTOMER:
                $this->userId = $token->getCustomerId();
                break;
            default:
                /* this is an unknown user type so reset the cached user type */
                $this->userType = null;
        }

        $this->isRequestProcessed = true;
    }
}
