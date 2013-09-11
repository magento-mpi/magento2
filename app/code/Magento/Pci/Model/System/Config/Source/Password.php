<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for admin password change mode
 *
 */
namespace Magento\Pci\Model\System\Config\Source;

class Password extends \Magento\Object
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 0,
                'label' => __('Recommended'),
            ),
            array(
                'value' => 1,
                'label' => __('Forced'),
            ),
        );
    }
}
