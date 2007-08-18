<?php
/**
 * Adminhtml dashboard graph axis abstract
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

abstract class Mage_Adminhtml_Block_Dashboard_Tab_Graph_Axis_Abstract extends Mage_Core_Block_Abstract
{
	protected $_collection = null;
	protected $_labelFilter = null;
	protected $_labels = null;
	
	const DIRECTION_HORIZONTAL = 'horizontal';
	const DIRECTION_VERTICAL   = 'vertical';
	
	public function setCollection($collection)
	{
		$this->_collection = $collection;
		return $this;
	}
	
	public function getCollection()
	{
		if(is_null($this->_collection)) {
			$this->_collection = $this->getParentBlock()->getCollection();
		}
		
		return $this->_collection;
	}

	public function getLabels()
	{
		if(is_null($this->_labels)) {
			$this->_initLabels();
		} 
		
		return $this->_labels;
	}
	
	protected function _initLabels()
	{
		$this->_labels = array();
		return $this;
	}
	
	abstract public function getDirection();
		
	public function setLabelFilter(Zend_Filter_Interface $filter)
	{
		$this->_labelFilter = $filter;
		return $this;
	}
	
	public function getLabelFilter()
	{
		return $this->_labelFilter;
	}
	
	public function getLabelText($value)
	{
		if($this->getLabelFilter()) {
			return $this->getLabelFilter()->filter($value);
		}
		
		return $value;
	}
	
	public function getTitle() 
	{
		return $this->getData('title');
	}
	
	public function setTitle($title)
	{
		$this->setData('title', $title);
		return $this;
	}
	
	public function getHorizontalDirectionConstant()
	{
		return self::DIRECTION_HORIZONTAL;
	}
	
	public function getVerticalDirectionConstant()
	{
		return self::DIRECTION_VERTICAL;
	}
	
	public function getPixelPosition($value) 
	{
		return $value;
	}
	
	public function getSpan()
	{
		return sizeof($this->getLabels()) + 1;
	}
	
	public function getPixelMaximum($item)
	{
		return 0;
	}
}// Class Mage_Adminhtml_Block_Graph_Axis_Abstract END