<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Interception\Custom\Module\Model\Item;

class Enhanced extends \Magento\Interception\Custom\Module\Model\Item
{
    /**
     * @return string
     */
    public function getName()
    {
        return ucfirst(parent::getName());
    }
}
