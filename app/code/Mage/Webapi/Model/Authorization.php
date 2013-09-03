<?php
/**
 * Web API authorization model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Authorization
{
    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var Mage_Webapi_Helper_Data
     */
    protected $_helper;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Helper_Data $helper
     * @param Magento_AuthorizationInterface $authorization
     */
    public function __construct(
        Mage_Webapi_Helper_Data $helper,
        Magento_AuthorizationInterface $authorization
    ) {
        $this->_helper = $helper;
        $this->_authorization = $authorization;
    }

    /**
     * Check permissions on specific service in ACL.
     *
     * @param string $service
     * @param string $method
     * @throws Mage_Webapi_Exception
     */
    public function checkServiceAcl($service, $method)
    {
        $coreAuthorization = $this->_authorization;
        if (!$coreAuthorization->isAllowed($service . Mage_Webapi_Model_Acl_Rule::RESOURCE_SEPARATOR . $method)
            && !$coreAuthorization->isAllowed(null)
        ) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('Access to service is forbidden.'),
                Mage_Webapi_Exception::HTTP_FORBIDDEN
            );
        }
    }
}
