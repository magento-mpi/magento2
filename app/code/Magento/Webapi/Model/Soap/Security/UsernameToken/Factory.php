<?php
/**
 * Factory of username token builders.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Soap_Security_UsernameToken_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create username token.
     *
     * @param array $arguments
     * @return Magento_Webapi_Model_Soap_Security_usernameToken
     */
    public function create($arguments = array())
    {
        return $this->_objectManager->create('Magento_Webapi_Model_Soap_Security_UsernameToken', $arguments);
    }
}
