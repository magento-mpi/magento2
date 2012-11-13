<?php
/**
 * Factory of username token builders.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Soap_Security_UsernameTokenFactory
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
     * @return Mage_Webapi_Model_Soap_Security_usernameToken
     */
    public function createFromArray($arguments = array())
    {
        return $this->_objectManager->create('Mage_Webapi_Model_Soap_Security_UsernameToken', $arguments);
    }
}
