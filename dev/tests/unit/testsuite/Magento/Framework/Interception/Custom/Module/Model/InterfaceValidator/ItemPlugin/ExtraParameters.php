<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\Interception\Custom\Module\Model\InterfaceValidator\ItemPlugin;

class ExtraParameters
{
    /**
     * @param \Magento\Framework\Interception\Custom\Module\Model\InterfaceValidator\Item $subject
     * @param string $name
     * @param string $surname
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetItem(
        \Magento\Framework\Interception\Custom\Module\Model\InterfaceValidator\Item $subject, $name, $surname
    ) {
        return $name . $surname;
    }
}
