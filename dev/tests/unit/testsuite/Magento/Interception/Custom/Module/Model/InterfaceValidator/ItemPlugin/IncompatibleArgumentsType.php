<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemPlugin;

class IncompatibleArgumentsType
{
    /**
     * @param \Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemWithArguments $subject
     * @param array $names
     * @return int
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetItem(
        \Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemWithArguments $subject, array $names
    ) {
        return count($names);
    }
}
