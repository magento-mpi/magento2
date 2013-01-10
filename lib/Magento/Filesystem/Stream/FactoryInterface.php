<?php
/**
 * Interface of Magento filesystem stream factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Filesystem_Stream_FactoryInterface
{
    /**
     * Create stream object
     *
     * @param string $key
     * @return Magento_Filesystem_StreamInterface
     */
    public function createStream($key);
}
