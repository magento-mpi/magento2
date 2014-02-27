<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Cache\Type;

/**
 * In-memory readonly pool of cache front-ends with enforced access control, specific to cache types
 */
class FrontendPool
{
    /**
     * @var \Magento\ObjectManager
     */
    private $_objectManager;

    /**
     * @var \Magento\App\Arguments
     */
    private $_arguments;

    /**
     * @var \Magento\App\Cache\Frontend\Pool
     */
    private $_frontendPool;

    /**
     * @var array
     */
    private $_typeFrontendMap;

    /**
     * @var \Magento\Cache\FrontendInterface[]
     */
    private $_instances = array();

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\App\Arguments $arguments
     * @param \Magento\App\Cache\Frontend\Pool $frontendPool
     * @param array $typeFrontendMap Format: array('<cache_type_id>' => '<cache_frontend_id>', ...)
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\App\Arguments $arguments,
        \Magento\App\Cache\Frontend\Pool $frontendPool,
        array $typeFrontendMap = array()
    ) {
        $this->_objectManager = $objectManager;
        $this->_arguments = $arguments;
        $this->_frontendPool = $frontendPool;
        $this->_typeFrontendMap = $typeFrontendMap;
    }

    /**
     * Retrieve cache frontend instance by a cache type identifier, enforcing identifier-scoped access control
     *
     * @param string $cacheType Cache type identifier
     * @return \Magento\Cache\FrontendInterface Cache frontend instance
     */
    public function get($cacheType)
    {
        if (!isset($this->_instances[$cacheType])) {
            $frontendId = $this->_getCacheFrontendId($cacheType);
            $frontendInstance = $this->_frontendPool->get($frontendId);
            /** @var $frontendInstance AccessProxy */
            $frontendInstance = $this->_objectManager->create(
                'Magento\App\Cache\Type\AccessProxy', array(
                    'frontend' => $frontendInstance,
                    'identifier' => $cacheType,
                )
            );
            $this->_instances[$cacheType] = $frontendInstance;
        }
        return $this->_instances[$cacheType];
    }

    /**
     * Retrieve cache frontend identifier, associated with a cache type
     *
     * @param string $cacheType
     * @return string
     */
    protected function _getCacheFrontendId($cacheType)
    {
        $result = $this->_arguments->getCacheTypeFrontendId($cacheType);
        if (!$result) {
            if (isset($this->_typeFrontendMap[$cacheType])) {
                $result = $this->_typeFrontendMap[$cacheType];
            } else {
                $result = \Magento\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID;
            }
        }
        return $result;
    }
}
