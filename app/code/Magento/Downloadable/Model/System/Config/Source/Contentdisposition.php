<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Model\System\Config\Source;

/**
 * Downloadable Content Disposition Source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Contentdisposition implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'attachment', 'label' => __('attachment')),
            array('value' => 'inline', 'label' => __('inline'))
        );
    }
}
