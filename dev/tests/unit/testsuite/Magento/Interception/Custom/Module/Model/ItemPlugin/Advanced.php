<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\Custom\Module\Model\ItemPlugin;

class Advanced
{
    /**
     * @param $subject
     * @param $proceed
     * @param $argument
     * @return string
     */
    public function aroundGetName($subject, $proceed, $argument)
    {
        return '[' . $proceed($argument) . ']';
    }

    /**
     * @param $subject
     * @param $result
     * @return string
     */
    public function afterGetName($subject, $result)
    {
        return $result . '%';
    }
}
