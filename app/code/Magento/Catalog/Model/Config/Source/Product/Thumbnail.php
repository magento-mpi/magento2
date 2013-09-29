<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog products per page on Grid mode source
 *
 * @category   Magento
 * @package    Magento_Catalog
 */
namespace Magento\Catalog\Model\Config\Source\Product;

class Thumbnail implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'itself', 'label'=>__('Product Thumbnail Itself')),
            array('value'=>'parent', 'label'=>__('Parent Product Thumbnail')),
        );
    }
}
