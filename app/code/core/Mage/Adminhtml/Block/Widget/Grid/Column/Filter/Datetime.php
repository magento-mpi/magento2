<?php
/**
 * Date grid column filter
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @todo        date format
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Datetime extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Date
{

    public function getValue($index=null)
    {
        if ($index) {
            if ($data = $this->getData('value', $index)) {
                return $data;//date('Y-m-d', strtotime($data));
            }
            return null;
        }
        $value = $this->getData('value');
        if (is_array($value)) {
            $value['datetime'] = true;
        }
        return $value;
    }

}