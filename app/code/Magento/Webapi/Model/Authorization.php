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
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(
        \Magento\AuthorizationInterface $authorization
    ) {
        $this->_authorization = $authorization;
    }

    /**
     * Check permissions on specific resource in ACL.
     *
     * @param string $resource
     * @param string $method
     * @throws Magento_Webapi_Exception
     */
    public function checkResourceAcl($resource, $method)
    {
        $coreAuthorization = $this->_authorization;
        if (!$coreAuthorization->isAllowed($resource . Magento_Webapi_Model_Acl_Rule::RESOURCE_SEPARATOR . $method)
            && !$coreAuthorization->isAllowed(null)
        ) {
            throw new Magento_Webapi_Exception(
                __('Access to resource is forbidden.'),
                Magento_Webapi_Exception::HTTP_FORBIDDEN
            );
        }
    }
}
