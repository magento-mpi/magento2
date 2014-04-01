<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemPlugin;

class ValidPlugin
{
    /**
     * @param \Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemWithArguments $subject
     * @param string $result
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetItem(
        \Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemWithArguments $subject, $result
    ) {
        return $result . '!';
    }

    /**
     * @param \Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemWithArguments $subject
     * @param $name
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetItem(
        \Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemWithArguments $subject, $name
    ) {
        return '|' . $name;
    }

    /**
     * @param \Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemWithArguments $subject
     * @param Closure $proceed
     * @param string $name
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetItem(
        \Magento\Interception\Custom\Module\Model\InterfaceValidator\ItemWithArguments $subject,
        \Closure $proceed,
        $name
    ) {
        $proceed('&' . $name . '&');
    }
}
