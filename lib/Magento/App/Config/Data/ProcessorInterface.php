<?php
/**
 * Processor interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config\Data;

interface ProcessorInterface
{
    /**
     * Process config value
     *
     * @param string $value
     * @return mixed
     */
    public function processValue($value);
}
