<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\Custom\Module\Model\ItemPlugin;

class Simple
{
    /**
     * @param string $invocationResult
     * @return string
     */
    public function afterGetName($invocationResult)
    {
        return $invocationResult . '!';
    }
}
