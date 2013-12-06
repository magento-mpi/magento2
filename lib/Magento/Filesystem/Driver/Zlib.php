<?php
/**
 * Magento filesystem zlib driver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Driver;

class Zlib extends \Magento\Filesystem\Driver\Local
{
    /**
     * @var string
     */
    protected $scheme = 'compress.zlib';
}
