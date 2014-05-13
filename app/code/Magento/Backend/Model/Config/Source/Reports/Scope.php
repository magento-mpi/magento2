<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config source reports event store filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Source\Reports;

class Scope implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Scope filter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'website', 'label' => __('Website')),
            array('value' => 'group', 'label' => __('Store')),
            array('value' => 'store', 'label' => __('Store View'))
        );
    }
}
