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
 
class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	protected static $_statuses = array(
		Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE   => 'Not activated',
		Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED   => 'Subcribed',
		Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED => 'Unsubcribed'
	);
		
    public function render(Varien_Object $row)
    {
    	return __($this->getStatus($row->getData($this->getColumn()->getIndex())));
    }
    
    public static function  getStatus($status) 
    {
    	if(isset(self::$_statuses[$status])) {
    		return self::$_statuses[$status];
    	}
    	
    	return 'Unknown';
    }
}