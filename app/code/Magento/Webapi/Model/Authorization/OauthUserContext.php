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
use Magento\Integration\Model\Integration\Factory as IntegrationFactory;
use Magento\Webapi\Controller\Request;

class OauthUserContext implements UserContextInterface
{
    /**
     * @var Request
     */
    protected $_request;

    /**
     * @var IntegrationFactory
     */
    protected $_integrationFactory;

    /**
     * Initialize dependencies.
     *
     * @param Request $request
     * @param IntegrationFactory $integrationFactory
     */
    public function __construct(
        Request $request,
        IntegrationFactory $integrationFactory
    ) {
        $this->_request = $request;
        $this->_integrationFactory = $integrationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        $consumerId = $this->_request->getConsumerId();
        $integration = $this->_integrationFactory->create()->loadByConsumerId($consumerId);
        return $integration->getId() ? (int)$integration->getId() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserType()
    {
        return UserIdentifier::USER_TYPE_INTEGRATION;
    }
}
