<?php
/**
 * Checkbox grid column filter
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Checkbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select 
{
    protected function _getOptions()
    {
        return array(
            array(
                'label' => __('Any'),
                'value' => ''
            ),
            array(
                'label' => __('Yes'),
                'value' => 1
            ),
            array(
                'label' => __('No'),
                'value' => 0
            ),
        );
    }
}