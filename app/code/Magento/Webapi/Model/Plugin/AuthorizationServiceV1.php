<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Plugin;

use Magento\Code\Plugin\InvocationChain;
use Magento\Authz\Model\UserIdentifier;
use Magento\Integration\Service\IntegrationV1 as IntegrationService;
use Magento\Integration\Model\Integration;
use Magento\Logger;

/**
 * Wrap isAllowed() method from AuthorizationV1 service to avoid checking roles of deactivated integration.
 */
class AuthorizationServiceV1
{
    /** @var IntegrationService */
    protected $_integrationService;

    /** @var Logger */
    protected $_logger;

    /** @var UserIdentifier */
    protected $_userIdentifier;

    /**
     * Inject dependencies.
     *
     * @param IntegrationService $integrationService
     * @param Logger             $logger
     * @param UserIdentifier     $userIdentifier
     */
    public function __construct(IntegrationService $integrationService, Logger $logger, UserIdentifier $userIdentifier)
    {
        $this->_integrationService = $integrationService;
        $this->_logger = $logger;
        $this->_userIdentifier = $userIdentifier;
    }

    /**
     * Check whether integration is inactive and don't allow using this integration in this case.
     *
     * It's ok that we break invocation chain since we're dealing with ACL here - if something is not allowed at any
     * point it couldn't be made allowed at some other point.
     *
     * @param array           $arguments
     * @param InvocationChain $invocationChain
     * @return bool
     */
    public function aroundIsAllowed(array $arguments, InvocationChain $invocationChain)
    {
        /** @var UserIdentifier $userIdentifier */
        $userIdentifier = $arguments[1] ?: $this->_userIdentifier;

        if ($userIdentifier->getUserType() !== UserIdentifier::USER_TYPE_INTEGRATION) {
            return $invocationChain->proceed($arguments);
        }

        try {
            $integration = $this->_integrationService->get($userIdentifier->getUserId());
        } catch (\Exception $e) {
            // Wrong integration ID or DB not reachable or whatever - give up and don't allow just in case
            $this->_logger->logException($e);
            return false;
        }

        if ($integration->getStatus() !== Integration::STATUS_ACTIVE) {
            return false;
        }

        return $invocationChain->proceed($arguments);
    }
}
