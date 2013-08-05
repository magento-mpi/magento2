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
 * Country customer grid column filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Grid_Filter_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{

    protected function _getOptions()
    {
        $options = Mage::getResourceModel('Mage_Directory_Model_Resource_Country_Collection')->load()->toOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>__('All countries')));
        return $options;
    }

}
