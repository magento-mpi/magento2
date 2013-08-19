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
 * Adminhtml review grid filter by type
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Review_Grid_Filter_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected function _getOptions()
    {
        return array(
              array('label'=>'', 'value'=>''),
              array('label'=>__('Administrator'), 'value'=>1),
              array('label'=>__('Customer'), 'value'=>2),
              array('label'=>__('Guest'), 'value'=>3)
        );
    }

    public function getCondition()
    {
        if ($this->getValue() == 1) {
            return 1;
        } elseif ($this->getValue() == 2) {
            return 2;
        } else {
            return 3;
        }
    }
}// Class Mage_Adminhtml_Block_Review_Grid_Filter_Type END
