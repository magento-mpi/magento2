<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * In-memory pool of cache front-ends with enforced access control
 */
class Mage_Core_Model_Cache_Frontend_Pool_AccessGateway
{
    /**
     * @var Magento_ObjectManager
     */
    private $_objectManager;

    /**
     * @var Mage_Core_Model_Cache_Frontend_Pool
     */
    private $_frontendPool;

    /**
     * @var Magento_Cache_FrontendInterface[]
     */
    private $_instances = array();

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Cache_Frontend_Pool $frontendPool
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Cache_Frontend_Pool $frontendPool
    ) {
        $this->_objectManager = $objectManager;
        $this->_frontendPool = $frontendPool;
    }

    /**
     * Retrieve cache frontend instance by its unique identifier, enforcing identifier-scoped access control
     *
     * @param string $identifier Cache frontend identifier
     * @return Magento_Cache_FrontendInterface Cache frontend instance
     */
    public function get($identifier)
    {
        if (!isset($this->_instances[$identifier])) {
            /** @var $instance Mage_Core_Model_Cache_Type_AccessProxy */
            $instance = $this->_objectManager->create(
                'Mage_Core_Model_Cache_Type_AccessProxy', array(
                    'frontend' => $this->_frontendPool->get($identifier),
                    'identifier' => $identifier,
                ), false
            );
            $this->_instances[$identifier] = $instance;
        }
        return $this->_instances[$identifier];
    }
}
