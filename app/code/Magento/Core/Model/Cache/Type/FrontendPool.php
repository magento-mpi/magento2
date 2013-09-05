<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * In-memory readonly pool of cache front-ends with enforced access control, specific to cache types
 */
class Magento_Core_Model_Cache_Type_FrontendPool
{
    /**
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * @var Magento_Core_Model_Cache_Frontend_Pool
     */
    private $_frontendPool;

    /**
     * @var \Magento\Cache\FrontendInterface[]
     */
    private $_instances = array();

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param Magento_Core_Model_Cache_Frontend_Pool $frontendPool
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        Magento_Core_Model_Cache_Frontend_Pool $frontendPool
    ) {
        $this->_objectManager = $objectManager;
        $this->_frontendPool = $frontendPool;
    }

    /**
     * Retrieve cache frontend instance by its unique identifier, enforcing identifier-scoped access control
     *
     * @param string $identifier Cache frontend identifier
     * @return \Magento\Cache\FrontendInterface Cache frontend instance
     */
    public function get($identifier)
    {
        if (!isset($this->_instances[$identifier])) {
            $frontendInstance = $this->_frontendPool->get($identifier);
            if (!$frontendInstance) {
                $frontendInstance = $this->_frontendPool->get(
                    Magento_Core_Model_Cache_Frontend_Pool::DEFAULT_FRONTEND_ID
                );
            }
            /** @var $frontendInstance Magento_Core_Model_Cache_Type_AccessProxy */
            $frontendInstance = $this->_objectManager->create(
                'Magento_Core_Model_Cache_Type_AccessProxy', array(
                    'frontend' => $frontendInstance,
                    'identifier' => $identifier,
                )
            );
            $this->_instances[$identifier] = $frontendInstance;
        }
        return $this->_instances[$identifier];
    }
}
