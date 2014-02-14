<?php
/**
 * Factory or authentication objects
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
namespace Magento\Outbound\Authentication;

use Magento\ObjectManager;
use Magento\Outbound\AuthenticationInterface;

class Factory
{
    /**
     * @var ObjectManager
     */
    private $_objectManager;

    /**
     * @var array representing the map for authentications and authentication classes
     */
    protected $_authenticationMap = array();

    /**
     * @param array $authenticationMap
     * @param ObjectManager $objectManager
     */
    public function __construct(
        array $authenticationMap,
        ObjectManager $objectManager
    ) {
        $this->_authenticationMap = $authenticationMap;
        $this->_objectManager = $objectManager;
    }

    /**
     * Returns an Authentication that matches the type specified within Endpoint
     *
     * @param string $authenticationType
     * @return AuthenticationInterface
     * @throws \LogicException
     */
    public function getAuthentication($authenticationType)
    {
        if (!isset($this->_authenticationMap[$authenticationType])) {
            throw new \LogicException("There is no authentication for the type given: {$authenticationType}");
        }

        $authentication =  $this->_objectManager->get($this->_authenticationMap[$authenticationType]);
        if (!$authentication instanceof AuthenticationInterface) {
            throw new \LogicException(
                "Authentication class for {$authenticationType} does not implement authentication interface"
            );
        }
        return $authentication;
    }

}
