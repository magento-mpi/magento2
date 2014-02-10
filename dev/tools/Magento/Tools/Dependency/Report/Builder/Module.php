<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency\Report\Builder;

/**
 * Modules dependencies report builder
 */
class Module extends AbstractBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function buildReportData($dependencies)
    {
        return $dependencies;
    }
}
