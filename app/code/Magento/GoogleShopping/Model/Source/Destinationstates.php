<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Source;

/**
 * Google Data Api destination states
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Destinationstates implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve option array with destinations
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => \Magento\Gdata\Gshopping\Extension\Control::DEST_MODE_DEFAULT, 'label' => __('Default')),
            array(
                'value' => \Magento\Gdata\Gshopping\Extension\Control::DEST_MODE_REQUIRED,
                'label' => __('Required')
            ),
            array('value' => \Magento\Gdata\Gshopping\Extension\Control::DEST_MODE_EXCLUDED, 'label' => __('Excluded'))
        );
    }
}
