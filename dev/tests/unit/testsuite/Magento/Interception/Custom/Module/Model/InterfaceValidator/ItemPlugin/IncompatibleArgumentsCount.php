<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemPlugin;

class IncompatibleArgumentsCount
{
    /**
     * @param \Magento\Interception\Custom\Module\Model\InterfaceValidator\Item $subject
     * @param string $name
     * @param string $surname
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetItem(
        \Magento\Interception\Custom\Module\Model\InterfaceValidator\Item $subject, $name, $surname
    ) {
        return $name . $surname;
    }
}
