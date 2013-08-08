<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_FullPageCache_Model_MetadataInterface
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
     * @return mixed
     */
    public function getMetadata($key);

    /**
     * Set metadata value for specified key
     *
     * @param string $key
     * @param string $value
     */
    public function setMetadata($key, $value);

    /**
     * Save metadata for cache in cache storage
     *
     * @param array $requestTags
     */
    public function saveMetadata(array $requestTags = array());
}
