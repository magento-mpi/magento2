<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Country grid filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected function _getOptions()
    {
        $options = Mage::getResourceModel('Mage_Directory_Model_Resource_Country_Collection')
            ->load()
            ->toOptionArray(false);
        array_unshift($options, array('value'=>'', 'label'=>Mage::helper('Mage_Cms_Helper_Data')->__('All Countries')));
        return $options;
    }
}
