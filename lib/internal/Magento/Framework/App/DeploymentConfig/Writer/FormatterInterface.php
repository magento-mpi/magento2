<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig\Writer;


interface FormatterInterface
{
    /**
     * Format deployment configuration
     *
     * @param array $data
     * @return string
     */
    public function format($data);
}
