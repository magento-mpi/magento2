<?php
/**
 * Adminhtml dashboard html/css grath block
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Adminhtml_Dashboard_Tab_Graph extends Mage_Adminhtml_Block_Dashboard_Tab_Abstract 
{
	protected $_horizontalAxis = null;
	protected $_verticalAxis = null;
	
	protected $_series = array();
	
	public function getHorizontalAxis()
	{
		if(is_null($this->_horizontalAxis)) {
			
		}
	}
	
	public function getVeriticalAxis()
	{
		
	}
	
}// Class Mage_Adminhtml_Block_Adminhtml_Dashboard_Tab_Graph END