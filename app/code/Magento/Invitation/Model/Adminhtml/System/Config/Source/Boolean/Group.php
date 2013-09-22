<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation source for reffered customer group system configuration
 */
namespace Magento\Invitation\Model\Adminhtml\System\Config\Source\Boolean;

class Group implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            1 => __('Same as Inviter'),
            0 => __('Default Customer Group from System Configuration')
        );
    }
}
