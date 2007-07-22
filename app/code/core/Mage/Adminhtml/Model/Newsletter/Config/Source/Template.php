<?php
/**
 * Adminhtml config system template source
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Model_Newsletter_Config_Source_Template
{
	public function toOptionArray()
	{
		if(!$collection = Mage::registry('config_system_template')) {
			$collection = Mage::getResourceModel('newsletter/template_collection')
				->useSystemTemplates(true)
				->load();
						
			Mage::register('config_system_template', $collection);
		}
		
		return $collection->toOptionArray();
	}
}// Class Mage_Adminhtml_Model_Newsletter_Config_Source_Template END
