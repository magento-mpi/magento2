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
     * @param \Magento\Tools\Dependency\Config $config
     * @param string $filename
     */
    public function write(Config $config, $filename);
}
