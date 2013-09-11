<?php
/**
 * Configuration value backend model interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Data;

interface BackendModelInterface
{
    /**
     * Process config value
     *
     * @param string $value
     * @return mixed
     */
    public function processValue($value);
}
