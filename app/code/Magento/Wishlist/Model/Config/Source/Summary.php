<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Wishlist\Model\Config\Source;

class Summary implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>__('Display number of items in wish list')),
            array('value'=>1, 'label'=>__('Display item quantities')),
        );
    }
}
