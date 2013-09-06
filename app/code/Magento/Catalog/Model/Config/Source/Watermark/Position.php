<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Watermark position config source model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Config_Source_Watermark_Position implements Magento_Core_Model_Option_ArrayInterface
{

    /**
     * Get available options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'stretch',         'label' => __('Stretch')),
            array('value' => 'tile',            'label' => __('Tile')),
            array('value' => 'top-left',        'label' => __('Top/Left')),
            array('value' => 'top-right',       'label' => __('Top/Right')),
            array('value' => 'bottom-left',     'label' => __('Bottom/Left')),
            array('value' => 'bottom-right',    'label' => __('Bottom/Right')),
            array('value' => 'center',          'label' => __('Center')),
        );
    }

}
