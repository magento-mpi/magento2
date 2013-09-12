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
namespace Magento\Core\Model\Cache\Type;

class FrontendPool
{
    /**
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * @var \Magento\Core\Model\Cache\Frontend\Pool
     */
    private $_frontendPool;

    /**
     * @var \Magento\Cache\FrontendInterface[]
     */
    private $_instances = array();

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\Cache\Frontend\Pool $frontendPool
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\Cache\Frontend\Pool $frontendPool
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
                    \Magento\Core\Model\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID
                );
            }
            /** @var $frontendInstance \Magento\Core\Model\Cache\Type\AccessProxy */
            $frontendInstance = $this->_objectManager->create(
                'Magento\Core\Model\Cache\Type\AccessProxy', array(
                    'frontend' => $frontendInstance,
                    'identifier' => $identifier,
                )
            );
            $this->_instances[$identifier] = $frontendInstance;
        }
        return $this->_instances[$identifier];
    }
}
