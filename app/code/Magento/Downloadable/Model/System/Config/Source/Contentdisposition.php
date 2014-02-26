<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Model\System\Config\Source;

/**
 * Downloadable Content Disposition Source
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Contentdisposition implements \Magento\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'attachment',
                'label' => __('attachment')
            ),
            array(
                'value' => 'inline',
                'label' => __('inline')
            )
        );
    }
}

