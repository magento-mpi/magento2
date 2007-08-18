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

class Mage_Adminhtml_Block_Dashboard_Tab_Graph extends Mage_Adminhtml_Block_Dashboard_Tab_Abstract 
{
	protected $_horizontalAxis = null;
	protected $_verticalAxis = null;
	
	protected $_allSeries = array();
	
	const DEFAULT_VAXIS = 'adminhtml/dashboard_tab_graph_axis_liniar';
	const DEFAULT_HAXIS = 'adminhtml/dashboard_tab_graph_axis_time';
	const SERIES_TYPE = 'adminhtml/dashboard_tab_graph_series';
			
	public function getHorizontalAxis()
	{
		if(is_null($this->_horizontalAxis)) {
			$this->setHorizontalAxis( $this->getData('horizontal_axis') );
		}
		
		return $this->_horizontalAxis;
	}
	
	protected function  _getTabTemplate()
	{
		return 'dashboard/tab/graph.phtml';
	}
	
	public function getVeriticalAxis()
	{
		if(is_null($this->_verticalAxis)) {
			$this->setVeriticalAxis( $this->getData('veritical_axis') );
		}
		
		return $this->_verticalAxis;
	}
	
	public function setHorizontalAxis($axisType=null)
	{
		if(is_null($axisType)) {
			$axisType = self::DEFAULT_HAXIS;
		}
		
		$this->_horizontalAxis = $this->getLayout()->createBlock($axisType);
		$this->setChild('horizontal_axis', $this->_horizontalAxis);
		return $this;
	}
	
	public function setVerticalAxis($axisType=null)
	{
		if(is_null($axisType)) {
			$axisType = self::DEFAULT_VAXIS;
		}
		
		$this->_verticalAxis = $this->getLayout()->createBlock($axisType);
		$this->setChild('vertical_axis', $this->_verticalAxis);
		return $this;
	}
	
	public function addSeries($seriesId, array $options)
	{
		$series = $this->getLayout()->createBlock(self::SERIES_TYPE);
		$series->setData($options);
		
		if(isset($options['x_field'])) {
			$series->setXField($options['x_field']);
		}
		
		if(isset($options['y_field'])) {
			$series->setYField($options['y_field']);
		}
		
		$this->setChild('series_' . $seriesId, $series);
		$this->_allSeries[] = $series;
		return $this;
	}
	
	public function getSeries($seriesId)
	{
		if($this->getChild('series_' . $seriesId)) {
			return $this->getChild('series_' . $seriesId);
		}
		
		return null;
	}
	
	public function getAllSeries()
	{
		return $this->_allSeries;
	}
}// Class Mage_Adminhtml_Block_Dashboard_Tab_Graph END