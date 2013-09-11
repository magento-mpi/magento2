<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model;

class Metadata implements \Magento\FullPageCache\Model\MetadataInterface
{
    /**
     * FPC cache model
     *
     * @var \Magento\FullPageCache\Model\Cache
     */
    protected $_fpcCache;

    /**
     * Cache service info
     *
     * @var mixed
     */
    protected $_metaData = null;

    /**
     * @var \Magento\FullPageCache\Model\Request\Identifier
     */
    protected $_requestIdentifier;

    /**
     * @param \Magento\FullPageCache\Model\Cache $fpcCache
     * @param \Magento\FullPageCache\Model\Request\Identifier $requestIdentifier
     */
    public function __construct(
        \Magento\FullPageCache\Model\Cache $fpcCache,
        \Magento\FullPageCache\Model\Request\Identifier $requestIdentifier
    ) {
        $this->_fpcCache = $fpcCache;
        $this->_requestIdentifier = $requestIdentifier;

        $cacheId = $this->_requestIdentifier->getRequestCacheId() . self::METADATA_CACHE_SUFFIX;
        $cacheMetadata = $this->_fpcCache->load($cacheId);
        if ($cacheMetadata) {
            $cacheMetadata = unserialize($cacheMetadata);
        }

        $this->_metaData = (empty($cacheMetadata) || !is_array($cacheMetadata)) ? array() : $cacheMetadata;
    }

    /**
     * Get metadata value for specified key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getMetadata($key)
    {
        return (isset($this->_metaData[$key])) ? $this->_metaData[$key] : null;
    }

    /**
     * Set metadata value for specified key
     *
     * @param string $key
     * @param string $value
     */
    public function setMetadata($key, $value)
    {
        $this->_metaData[$key] = $value;
    }

    /**
     * Save metadata for cache in cache storage
     *
     * @param array $requestTags
     */
    public function saveMetadata(array $requestTags = array())
    {
        $this->_fpcCache->save(
            serialize($this->_metaData),
            $this->_requestIdentifier->getRequestCacheId() . self::METADATA_CACHE_SUFFIX,
            $requestTags
        );
    }
}
