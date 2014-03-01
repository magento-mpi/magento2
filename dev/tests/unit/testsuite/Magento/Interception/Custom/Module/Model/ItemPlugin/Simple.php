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
     * @param $subject
     * @param $invocationResult
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetName($subject, $invocationResult)
    {
        return $invocationResult . '!';
    }
}
