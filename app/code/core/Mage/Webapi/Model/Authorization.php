<?php
/**
 * Web API authorization model.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Authorization extends Mage_Core_Model_Authorization
{
    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        array $data = array()
    ) {
        $this->_helper = $helperFactory->get('Mage_Webapi_Helper_Data');
        parent::__construct($data);
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
        try {
            if (!$this->isAllowed($resource . Mage_Webapi_Model_Acl_Rule::RESOURCE_SEPARATOR . $method)
                && !$this->isAllowed(Mage_Webapi_Model_Acl_Rule::API_ACL_RESOURCES_ROOT_ID)
            ) {
                throw new Mage_Webapi_Exception(
                    $this->_helper->__('Access to resource is forbidden.'),
                    Mage_Webapi_Exception::HTTP_FORBIDDEN
                );
            }
        } catch (Zend_Acl_Exception $e) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('Resource is not found.'),
                Mage_Webapi_Exception::HTTP_NOT_FOUND
            );
        }
    }
}
