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
    public function __construct(
        Magento_AuthorizationInterface $authorization
    ) {
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
<<<<<<< HEAD:app/code/Mage/Webapi/Model/Authorization.php
        if (!$coreAuthorization->isAllowed($service . Mage_Webapi_Model_Acl_Rule::RESOURCE_SEPARATOR . $method)
            && !$coreAuthorization->isAllowed(null)
        ) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('Access to service is forbidden.'),
                Mage_Webapi_Exception::HTTP_FORBIDDEN
=======
        if (!$coreAuthorization->isAllowed($resource . Magento_Webapi_Model_Acl_Rule::RESOURCE_SEPARATOR . $method)
            && !$coreAuthorization->isAllowed(null)
        ) {
            throw new Magento_Webapi_Exception(
                __('Access to resource is forbidden.'),
                Magento_Webapi_Exception::HTTP_FORBIDDEN
>>>>>>> upstream/develop:app/code/Magento/Webapi/Model/Authorization.php
            );
        }
    }
}
