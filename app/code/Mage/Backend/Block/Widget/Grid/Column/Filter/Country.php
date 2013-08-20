<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Country grid filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Grid_Column_Filter_Country extends Mage_Backend_Block_Widget_Grid_Column_Filter_Select
{
    protected function _getOptions()
    {
        $options = Mage::getResourceModel('Mage_Directory_Model_Resource_Country_Collection')
            ->load()
            ->toOptionArray(false);
        array_unshift($options,
            array('value'=>'', 'label'=>__('All Countries'))
        );
        return $options;
    }
}
