<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model;

interface MetadataInterface
{
    /**
     * Metadata cache suffix
     */
    const METADATA_CACHE_SUFFIX        = '_metadata';

    /**
     * Get metadata value for specified key
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getMetadata($key);

    /**
     * Set metadata value for specified key
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setMetadata($key, $value);

    /**
     * Save metadata for cache in cache storage
     *
     * @param array $requestTags
     * @return void
     */
    public function saveMetadata(array $requestTags = array());
}
