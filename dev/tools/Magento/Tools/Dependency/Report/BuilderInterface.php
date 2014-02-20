<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Report;

/**
 *  Builder Interface
 */
interface BuilderInterface
{
    /**
     * Build a report
     *
     * @param array $options
     */
    public function build(array $options);
}
