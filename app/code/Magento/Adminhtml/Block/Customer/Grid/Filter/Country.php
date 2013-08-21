<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Country customer grid column filter
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Grid_Filter_Country extends Magento_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{

    protected function _getOptions()
    {
        $options = Mage::getResourceModel('Magento_Directory_Model_Resource_Country_Collection')->load()->toOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>__('All countries')));
        return $options;
    }

}
