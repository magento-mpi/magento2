<?php
/**
 * Factory or authentication objects
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
class Magento_Outbound_Authentication_Factory
{
    /** @var Magento_Core_Model_ObjectManager  */
    private $_objectManager;

    /**
     * @var array representing the map for authentications and authentication classes
     */
    protected $_authenticationMap = array();

    /**
     * @param array $authenticationMap
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        array $authenticationMap,
        Magento_ObjectManager $objectManager
    ) {
        $this->_authenticationMap = $authenticationMap;
        $this->_objectManager = $objectManager;
    }

    /**
     * Returns an Authentication that matches the type specified within Endpoint
     *
     * @param string $authenticationType
     * @throws LogicException
     * @return Magento_Outbound_AuthenticationInterface
     */
    public function getAuthentication($authenticationType)
    {
        if (!isset($this->_authenticationMap[$authenticationType])) {
            throw new LogicException("There is no authentication for the type given: {$authenticationType}");
        }

        $authentication =  $this->_objectManager->get($this->_authenticationMap[$authenticationType]);
        if (!$authentication instanceof Magento_Outbound_AuthenticationInterface) {
            throw new LogicException(
                "Authentication class for {$authenticationType} does not implement authentication interface"
            );
        }
        return $authentication;
    }

}
