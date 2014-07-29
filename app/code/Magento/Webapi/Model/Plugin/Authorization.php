<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Plugin;

use Magento\Authz\Model\UserIdentifier;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Integration\Service\V1\Integration as IntegrationService;
use Magento\Integration\Model\Integration;
use Magento\Framework\Logger;

/**
 * Wrap isAllowed() method from Authorization interface to avoid checking roles of deactivated integration.
 */
class Authorization
{
    /** @var IntegrationService */
    protected $_integrationService;

    /** @var Logger */
    protected $_logger;

    /** @var UserContextInterface */
    protected $_userContext;

    /**
     * Inject dependencies.
     *
     * @param IntegrationService $integrationService
     * @param Logger $logger
     * @param UserContextInterface $userContext
     */
    public function __construct(
        IntegrationService $integrationService,
        Logger $logger,
        UserContextInterface $userContext
    ) {
        $this->_integrationService = $integrationService;
        $this->_logger = $logger;
        $this->_userContext = $userContext;
    }

    /**
     * Check whether integration is inactive and don't allow using this integration in this case.
     *
     * It's ok that we break invocation chain since we're dealing with ACL here - if something is not allowed at any
     * point it couldn't be made allowed at some other point.
     *
     * @param \Magento\Framework\Authorization $subject
     * @param callable $proceed
     * @param string $resource
     * @param string $privilege
     *
     * @return bool
     */
    public function aroundIsAllowed(
        \Magento\Framework\Authorization $subject,
        \Closure $proceed,
        $resource,
        $privilege = null
    ) {
        if ($this->_userContext->getUserType() !== UserIdentifier::USER_TYPE_INTEGRATION) {
            return $proceed($resource, $privilege);
        }

        try {
            $integration = $this->_integrationService->get($this->_userContext->getUserId());
        } catch (\Exception $e) {
            // Wrong integration ID or DB not reachable or whatever - give up and don't allow just in case
            $this->_logger->logException($e);
            return false;
        }

        if ($integration->getStatus() !== Integration::STATUS_ACTIVE) {
            return false;
        }

        return $proceed($resource, $privilege);
    }
}
