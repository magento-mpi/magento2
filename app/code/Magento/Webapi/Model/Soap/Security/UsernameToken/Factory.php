<?php
/**
 * Factory of username token builders.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Soap\Security\UsernameToken;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
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
        return $this->_objectManager->create('Magento\Webapi\Model\Soap\Security\UsernameToken', $arguments);
    }
}
