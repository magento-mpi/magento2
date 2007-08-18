<?php
/**
 * Admihtml dashboard graph series block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Adminhtml_Block_Dashboard_Tab_Graph_Series extends Mage_Core_Block_Abstract 
 {
 	protected $_xField = 'x';
 	protected $_yField = 'y';
 	
 	public function setXField($field)
 	{
 		$this->_xField = $field;
 		return $this;
 	}
 	
 	public function setYField($field)
 	{
 		$this->_yField = $field;
 		return $this;
 	}
 	
 	public function getXField()
 	{
 		return $this->_xField;
 	}
 	
 	public function getYField()
 	{
 		return $this->_yField;
 	}
 	
 	public function getValue($item, $axis)
 	{
 		if ($axis->getDirection() == $axis->getHorizontalDirectionConstant()) {
 			$field = $this->getXField();
 		} else {
 			$field  = $this->getYField();
 		}
 		
 		if ($item instanceof Varien_Object) {
 			return $item->getData($field);
 		} else if (is_array($item) && isset($item[$field]))  {
 			return $item[$field];
 		}
 		
 		return null;
 	}
 	
 	
 } // Class Mage_Adminhtml_Block_Dashboard_Tab_Graph_Series end