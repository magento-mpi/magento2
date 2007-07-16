<?php
/**
 * Adminhtml newsletter subscribers grid website renderer
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Website extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
	public function render(Varien_Object $row) 
	{
		return Mage::getSingleton('core/website')->getName();
	}
}// Class  Mage_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Website END