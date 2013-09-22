<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable Content Disposition Source
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Model\System\Config\Source;

class Contentdisposition implements \Magento\Core\Model\Option\ArrayInterface
{
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

