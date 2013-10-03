<?php
/**
 * Interface of Magento filesystem stream factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Stream;

interface FactoryInterface
{
    /**
     * Create stream object
     *
     * @param string $key
     * @return \Magento\Filesystem\StreamInterface
     */
    public function createStream($key);
}
