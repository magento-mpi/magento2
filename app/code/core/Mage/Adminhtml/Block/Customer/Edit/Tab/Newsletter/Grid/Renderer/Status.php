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
		Mage_Newsletter_Model_Queue::STATUS_SENT 	=> 'Status sent',
		Mage_Newsletter_Model_Queue::STATUS_CANCEL	=> 'Status cancel',
		Mage_Newsletter_Model_Queue::STATUS_NEVER 	=> 'Status not sending',
		Mage_Newsletter_Model_Queue::STATUS_SENDING => 'Status sending',
		Mage_Newsletter_Model_Queue::STATUS_PAUSE 	=> 'Status pause'
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
    	
    	return 'status unknown';
    }
}