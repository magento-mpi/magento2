<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\Source\Product;

/**
 * Catalog products per page on Grid mode source
 *
 */
class Thumbnail implements \Magento\Framework\Option\ArrayInterface
{
    const OPTION_USE_PARENT_IMAGE = 'parent';

    const OPTION_USE_OWN_IMAGE = 'itself';

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::OPTION_USE_OWN_IMAGE, 'label' => __('Product Thumbnail Itself')),
            array('value' => self::OPTION_USE_PARENT_IMAGE, 'label' => __('Parent Product Thumbnail'))
        );
    }
}
