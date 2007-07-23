<?php
/**
 * Adminhtml newsletter queue grid block status item renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 
class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	protected static $_statuses = array(
		Mage_Newsletter_Model_Queue::STATUS_SENT 	=> 'Sent',
		Mage_Newsletter_Model_Queue::STATUS_CANCEL	=> 'Cancel',
		Mage_Newsletter_Model_Queue::STATUS_NEVER 	=> 'Never send',
		Mage_Newsletter_Model_Queue::STATUS_SENDING => 'Sending',
		Mage_Newsletter_Model_Queue::STATUS_PAUSE 	=> 'Paused'
	);
	
    public function render(Varien_Object $row)
    {
    	return __($this->getStatus($row->getQueueStatus()));
    }
    
    public static function  getStatus($status) 
    {
    	if(isset(self::$_statuses[$status])) {
    		return self::$_statuses[$status];
    	}
    	
    	return 'Unknown';
    }
}