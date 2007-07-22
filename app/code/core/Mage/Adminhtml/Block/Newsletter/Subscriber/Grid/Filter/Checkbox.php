<?php
/**
 * Adminhtml newsletter subscribers grid filter checkbox
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid_Filter_Checkbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract
{
	 public function getCondition()
    {
        return array();
    }
    
    public function getHtml()
    {
        return '<input type="checkbox" onclick="subscriberController.checkCheckboxes(this)"/>';
    }
}// Class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid_Filter_Checkbox END