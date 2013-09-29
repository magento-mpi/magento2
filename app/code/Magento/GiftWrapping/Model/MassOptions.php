<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Users
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User statuses option array
 *
 * @category   Magento
 * @package    Magento_GiftWrapping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Model;

class MassOptions implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('label' => '', 'value' => ''),
            array('label' => __('Enabled'), 'value' => '1'),
            array('label' => __('Disabled'), 'value' => '0')
        );
    }
}
