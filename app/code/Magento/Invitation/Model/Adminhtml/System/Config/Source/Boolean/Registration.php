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
 * Invitation config source for customer registration field
 */
namespace Magento\Invitation\Model\Adminhtml\System\Config\Source\Boolean;

class Registration implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            1 => __('By Invitation Only'),
            0 => __('Available to All')
        );
    }
}
