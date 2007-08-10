<?php
/**
 * Adminhtml catalog super product link grid checkbox renderer
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid_Renderer_Checkbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Checkbox
{
	/**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {	
    	$result = parent::render($row);
    	return $result.'<input type="hidden" class="value-json" value="'.htmlspecialchars($this->getAttributesJson($row)).'" />';
    }
    
    public function getAttributesJson(Varien_Object $row)
    {
    	if(!$this->getColumn()->getAttributes()) {
    		return '[]';
    	}
    	
    	$result = array();
    	foreach($this->getColumn()->getAttributes() as $attribute) {
    		if($attribute->getSourceModel()) {
    			$label = $attribute->getSource()->getOptionText($row->getData($attribute->getAttributeCode()));    			
    		} else {
    			$label = $row->getData($attribute->getAttributeCode());
    		}
    		
    		$item = array();
    		$item['label'] = $label;
    		$item['attribute_id'] = $attribute->getAttributeId();
    		$item['value_index']	= $row->getData($attribute->getAttributeCode());
    		$result[] = $item;
    	}
    	
    	return Zend_Json::encode($result);
    }
}// Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid_Renderer_Checkbox END