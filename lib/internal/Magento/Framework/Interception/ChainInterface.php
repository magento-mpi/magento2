<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Interception;

interface ChainInterface
{
    /**
     * @param string $type
     * @param string $method
     * @param string $subject
     * @param array $arguments
     * @param string $previousPluginCode
     * @return mixed
     */
    public function invokeNext($type, $method, $subject, array $arguments, $previousPluginCode = null);
}
