<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ogone template Action Dropdown source
 */
namespace Magento\Ogone\Model\Source;

class Pmlist implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Prepare ogone payment block layout as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => \Magento\Ogone\Model\Api::PMLIST_HORISONTAL_LEFT, 'label' => __('Horizontally grouped logo with group name on left')),
            array('value' => \Magento\Ogone\Model\Api::PMLIST_HORISONTAL, 'label' => __('Horizontally grouped logo with no group name')),
            array('value' => \Magento\Ogone\Model\Api::PMLIST_VERTICAL, 'label' => __('Verical list')),
        );
    }
}
