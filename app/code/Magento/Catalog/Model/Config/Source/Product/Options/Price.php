<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\Source\Product\Options;

/**
 * Price types mode source
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Price implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'fixed', 'label' => __('Fixed')),
            array('value' => 'percent', 'label' => __('Percent'))
        );
    }
}
