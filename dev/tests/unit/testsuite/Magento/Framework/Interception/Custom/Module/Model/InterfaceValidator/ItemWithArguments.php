<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Interception\Custom\Module\Model\InterfaceValidator;

class ItemWithArguments
{
    /**
     * @param string $name
     * @return string
     */
    public function getItem($name = 'default')
    {
        return $name;
    }
}
