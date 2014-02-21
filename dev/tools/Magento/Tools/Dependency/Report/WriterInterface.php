<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Report;

use Magento\Tools\Dependency\Report\Data\ConfigInterface;

/**
 *  Writer Interface
 */
interface WriterInterface
{
    /**
     * Write a report file
     *
     * @param array $options
     * @param \Magento\Tools\Dependency\Report\Data\ConfigInterface $config
     */
    public function write(array $options, ConfigInterface $config);
}
