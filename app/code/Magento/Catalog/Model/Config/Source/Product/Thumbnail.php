<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\Source\Product;

/**
 * Catalog products per page on Grid mode source
 *
 * @category   Magento
 * @package    Magento_Catalog
 */
class Thumbnail implements \Magento\Core\Model\Option\ArrayInterface
{
    const OPTION_USE_PARENT_IMAGE = 'parent';
    const OPTION_USE_OWN_IMAGE = 'itself';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::OPTION_USE_OWN_IMAGE, 'label' => __('Product Thumbnail Itself')),
            array('value' => self::OPTION_USE_PARENT_IMAGE, 'label' => __('Parent Product Thumbnail')),
        );
    }
}
