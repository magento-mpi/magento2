<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Source model for admin password change mode
 *
 */
namespace Magento\Pci\Model\System\Config\Source;

class Password extends \Magento\Framework\Object implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 0, 'label' => __('Recommended')], ['value' => 1, 'label' => __('Forced')]];
    }
}
