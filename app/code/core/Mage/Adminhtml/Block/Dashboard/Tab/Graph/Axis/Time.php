<?
/**
 * Adminhtml dashboard tab graph axis abstract
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Adminhtml_Block_Dashboard_Tab_Graph_Axis_Time extends Mage_Adminhtml_Block_Dashboard_Tab_Graph_Axis_Abstract 
 {
 	
 	
 	protected function _initLabels()
 	{
 		$labelValues = array();
 		
 		
 		foreach ($this->getParentBlock()->getAllSeries() as $series) {
 			foreach ($this->getCollection() as $item) {
 				$time = $series->getValue($item, $this);
 				if(!is_int($time)) {
 					$time = strtotime($time);
 				}
 				
				$labelValues[] = $time;
 			}
 		}
 		
 		$labelValues = array_unique($labelValues);
 		$timeFormat = $this->getFormatByPeriod($minTime, $maxTime);
 		
 		foreach ($labelValues as $value) {
 			$label = strftime($timeFormat, $value);
 			
 			$this->_labels[] = $this->getLabelText($label);
 		}
 		
 		return $this;
 	}
 	
 	public function getFormat() 
 	{
 		if($this->getData('format')) {
 			return $this->getData('format');
 		}
 		 				
 		switch (strtolower($this->getFormatType())) {
 			case "time":
 				return $this->getTimeFormat();
 				break;
 			
 			case "week":
 				return $this->getWeekFormat();
 				break;
 				
 			case "month":
 				return $this->getMonthFormat();
 				break;
 				
 			case "year":
 				return $this->getYearFormat();
 				break;
 			
 			case "day":
 			default: // Default labels format
 				return $this->getDateFormat();
 				break;
 		}
 	}
 	
 	public function getTimeFormat() 
 	{
 		if(!$this->getData('time_format')) {
 			return '%I:%M %p';
 		}
 		
 		return $this->getData('time_format');
 	}
 	
 	public function getWeekFormat() 
 	{
 		if(!$this->getData('week_format')) {
 			return $this->getDateFormat();
 		}
 		
 		return $this->getData('week_format');
 	}
 	
 	public function getMonthFormat() 
 	{
 		if(!$this->getData('month_format')) {
 			return '%e %Y';
 		}
 		
 		return $this->getData('time_format');
 	}
 	
 	
 	public function getDateFormat() 
 	{
 		if(!$this->getData('date_format')) {
 			return Mage::getStoreConfig('general/local/date_format_short');
 		}
 		
 		return $this->getData('date_format');
 	}
 } // Class Mage_Adminhtml_Block_Dashboard_Tab_Graph_Axis_Time end