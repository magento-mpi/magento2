<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\Custom\Module\Model\ItemContainerPlugin;

class Simple
{
    /**
     * @param $subject
     * @param $invocationResult
     * @return string
     */
    public function afterGetName($subject, $invocationResult)
    {
        return $invocationResult . '|';
    }
}
