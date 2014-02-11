<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Report;

use Magento\Tools\Dependency\Config;

/**
 *  Writer Interface
 */
interface WriterInterface
{
    /**
     * Write a report file
     *
     * @param string $filename
     * @param \Magento\Tools\Dependency\Config $config
     */
    public function write($filename, Config $config);
}
