<?php
/**
 * Web API authorization model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Authorization
{
    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Initialize dependencies.
     *
     * @param Magento_AuthorizationInterface $authorization
     */
    public function __construct(Magento_AuthorizationInterface $authorization)
    {
        $this->_authorization = $authorization;
    }

    /**
     * Check permissions on specific service in ACL.
     *
     * @param string $service
     * @param string $method
     * @throws Magento_Webapi_Exception
     */
    public function checkServiceAcl($service, $method)
    {
        $coreAuthorization = $this->_authorization;
        if (!$coreAuthorization->isAllowed($service . Magento_Webapi_Model_Acl_Rule::RESOURCE_SEPARATOR . $method)
            && !$coreAuthorization->isAllowed(null)
        ) {
            throw new Magento_Webapi_Exception(
                __('Access to service is forbidden.'),
                0,
                Magento_Webapi_Exception::HTTP_FORBIDDEN
            );
        }
    }
}
