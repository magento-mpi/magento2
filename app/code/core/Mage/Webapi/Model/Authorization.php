<?php
/**
 * Web API authorization model.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Authorization
{
    /**
     * Web API ACL config's resources root ID.
     */
    const API_ACL_RESOURCES_ROOT_ID = 'Mage_Webapi';

    /** @var Mage_Core_Model_Authorization */
    protected $_coreAuthorization;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Core_Model_Authorization $coreAuthorization
     */
    public function __construct(
        Mage_Webapi_Helper_Data $helper,
        Mage_Core_Model_Authorization $coreAuthorization
    ) {
        $this->_helper = $helper;
        $this->_coreAuthorization = $coreAuthorization;
    }

    /**
     * Check permissions on specific resource in ACL.
     *
     * @param string $resource
     * @param string $method
     * @throws Mage_Webapi_Exception
     */
    public function checkResourceAcl($resource, $method)
    {
        $coreAuthorization = $this->_coreAuthorization;
        if (!$coreAuthorization->isAllowed($resource . Mage_Webapi_Model_Acl_Rule::RESOURCE_SEPARATOR . $method)
            && !$coreAuthorization->isAllowed(Mage_Webapi_Model_Authorization::API_ACL_RESOURCES_ROOT_ID)
        ) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('Access to resource is forbidden.'),
                Mage_Webapi_Exception::HTTP_FORBIDDEN
            );
        }
    }
}
