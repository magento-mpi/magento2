<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Adminhtml_Block_Dashboard_Graph extends Mage_Adminhtml_Block_Dashboard_Abstract
{
    protected $_allSeries = array();
    protected $_axisLabels = array();
    protected $_axisMaps = array();

    protected $_dataRows = array();

    protected $_simpleEncoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    protected $_extendedEncoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';
    protected $_apiUrl = 'http://chart.apis.google.com/chart?';
    protected $_size = '830x300';
    // Google Chart Api Data Encoding
    protected $_encoding = 'e';

    protected $_htmlId = '';

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('dashboard/graph.phtml');
    }

    protected function  _getTabTemplate()
    {
        return 'dashboard/graph.phtml';
    }

    public function setDataRows($rows)
    {
        $this->_dataRows = (array)$rows;
    }

    public function addSeries($seriesId, array $options)
    {
        $this->_allSeries[$seriesId] = $options;
    }

    public function getSeries($seriesId)
    {
        if (isset($this->_allSeries[$seriesId])) {
            return $this->_allSeries[$seriesId];
        } else {
            return false;
        }
    }

    public function getAllSeries()
    {
        return $this->_allSeries;
    }

    public function getChartUrl()
    {

        $this->_allSeries = $this->getRowsData($this->_dataRows);

        foreach ($this->_axisMaps as $axis => $attr){
            $this->setAxisLabels($axis, $this->getRowsData($attr, true));
        }

        // Google encoding values
    	if ($this->_encoding == "s") {
    		// simple encoding
    		$dataHeader .= "&chd=s:";
    		$dataDelimiter = "";
    		$dataSetdelimiter = ",";
    		$dataMissing = "_";
    	} else {
    		// extended encoding
    		$dataHeader = "&chd=e:";
    		$dataDelimiter = "";
    		$dataSetdelimiter = ",";
    		$dataMissing = "__";
    	}

    	// process each string in the array, and find the max length
    	foreach ($this->getAllSeries() as $index => $serie) {
    		// find length of each data set
    		$localmaxlength[$index] = sizeof($serie);

			// find max and min values
			$localmaxvalue[$index] = max($serie);
			$localminvalue[$index] = min($serie);
		}

    	// determine overall max values
    	if (is_numeric($this->_max)) {
    		// maximum value set in request
    		$maxvalue = $this->_max;
    	} else {
    		// determine from data
    		$maxvalue = max($localmaxvalue);
    	}
    	if (is_numeric($this->_min)) {
    		// minimum value set in request
    		$minvalue = $this->_min;
    	} else {
    		// determine from data
    		$minvalue = min($localminvalue);
    	}
    	$maxlength = max($localmaxlength);
        $valuepadding = 0.05;
    	// determine the full range of data for all data sets
    	if ($minvalue >= 0 && $maxvalue >= 0) {
    		// all numbers are positive, so the baseline = 0
    		$maxy = $maxvalue + ($maxvalue * $valuepadding); // pad the top
    		$miny = 0;
    		if ($maxy > 10) {
                $maxy = $this->Round($maxy, 0-round(strlen(floor($maxy))/2));
                $yLabels = range($miny, $maxy, ($maxy-$miny)/10);
            } else {
                $maxy = ceil($maxy);
    		    $yLabels = range($miny, $maxy, 1);
    		}
    		$yrange = $maxy;
    		$yorigin = 0;
    	}

    	// set up an array to handle the chart data
    	$chartdata = array();

    	// process each data set
    	foreach ($this->getAllSeries() as $index => $serie) {
    		// process each item in the array
    		$thisdataarray = $serie;
    		if ($this->_encoding == "s") {
    			// SIMPLE ENCODING
    			// process elements
    			for ($j = 0; $j < sizeof($thisdataarray); $j++) {
    				$currentvalue = $thisdataarray[$j];
    				if (is_numeric($currentvalue)) {
    					// map data to $this->_simpleEncoding string
    					$ylocation = round((strlen($this->_simpleEncoding)-1) * ($yorigin + $currentvalue) / $yrange);
    					// add point data
    					array_push($chartdata, substr($this->_simpleEncoding, $ylocation, 1) . $dataDelimiter);
    				} else {
    					// add empty point data
    					array_push($chartdata, $dataMissing . $dataDelimiter);
    				}
    			}
    			// END SIMPLE ENCODING
    		} else {
    			// EXTENDED ENCODING
    			// process elements
    			for ($j = 0; $j < sizeof($thisdataarray); $j++) {
    				$currentvalue = $thisdataarray[$j];
    				if (is_numeric($currentvalue)) {
    					// convert data to 0-4095 range
    					$ylocation = (4095 * ($yorigin + $currentvalue) / $yrange);
    					// find first character location (round down to integer)
    					$firstchar = floor($ylocation / 64);
    					// find second character location
    					$secondchar = $ylocation % 64; // modulus
    					// find combined location in $this->_extendedEncoding string
    					$mappedchar = substr($this->_extendedEncoding, $firstchar, 1) . substr($this->_extendedEncoding, $secondchar, 1);
    					// add point data
    					array_push($chartdata, $mappedchar . $dataDelimiter);
    				} else {
    					// add empty point data
    					array_push($chartdata, $dataMissing . $dataDelimiter);
    				}
    			}
    			// ============= END EXTENDED ENCODING =============
    		}
    		// add a set delimiter
    		array_push($chartdata, $dataSetdelimiter);
    	}

    	// get chart data and store it in a buffer
    	$buffer = implode('', $chartdata);

    	// remove any trailing or extra delimiters
    	$buffer = rtrim($buffer, $dataSetdelimiter);
    	$buffer = rtrim($buffer, $dataDelimiter);
    	$buffer = str_replace(($dataDelimiter . $dataSetdelimiter), $dataSetdelimiter, $buffer);

    	// draw chart labels if needed (x,y,r,t)
    	$labelBuffer = "";
        $valueBuffer = array();
        $rangeBuffer = "";

        if (sizeof($this->_axisLabels) > 0) {
    		$labelBuffer .= "&chxt=" . implode(',', array_keys($this->_axisLabels));
    		$indexid = 0;
    		foreach ($this->_axisLabels as $idx=>$labels){
    		    if ($idx == 'x') {
    		        $this->_axisLabels[$idx][sizeof($this->_axisLabels[$idx])-1] = '';
    		        $this->_axisLabels[$idx][0] = '';
                    array_map('urlencode', $this->_axisLabels[$idx]);
                    $valueBuffer[] = $indexid . ":|" . implode('|', $this->_axisLabels[$idx]);
                    if (sizeof($this->_axisLabels[$idx]) > 1) {
                        $deltaX = 100/(sizeof($this->_axisLabels[$idx])-1);
                    } else {
                        $deltaX = 100;
                    }
    		    } else if ($idx == 'y') {
                    $yLabels[sizeof($yLabels)-1] = '';
                    $valueBuffer[] = $indexid . ":|" . implode('|', $yLabels);
                    $deltaY = 100/(sizeof($yLabels)-1);
                    // setting range values for y axis
        			$rangeBuffer = $indexid . "," . $miny . "," . $maxy . "|";
    		    }
    		    $indexid++;
    		}
    		$labelBuffer .= "&chxl=" . implode('|', $valueBuffer);
    	};

    	// chart size
    	$chartSize = "&chs=".$this->_size;

    	if (isset($deltaX) && isset($deltaY)) {
    	    $gridLines = "&chg={$deltaX},{$deltaY},1,0";
    	} else {
    	    $gridLines = "";
    	}
    	// return the encoded data
        return $this->_apiUrl . $labelBuffer . $dataHeader . $buffer . $gridLines .
            $chartSize . "&cht=lc&chf=bg,s,f4f4f4|c,lg,90,ffffff,0.1,ededed,0" .
            "&chm=B,f4d4b2,0,0,0&chco=db4814&".rand();
    }

    protected function getRowsData($attributes, $single = false)
    {
        $items = $this->getCollection()->getItems();
        $options = array();
        foreach ($items as $item){
            if ($single) {
                $options[] = $item->getData($attributes);
            } else {
                foreach ((array)$attributes as $attr){
                    $options[$attr][] = $item->getData($attr);
                }
            }
        }
        return $options;
    }

    public function setAxisLabels($axis, $labels)
    {
        $this->_axisLabels[$axis] = $labels;
    }

    public function setHtmlId($htmlId)
    {
        $this->_htmlId = $htmlId;
    }

    public function getHtmlId()
    {
        return $this->_htmlId;
    }

    function Round($n, $dp)
    {
        if(round($n, $dp) > $n) {
            return ceil($n*pow(10, $dp))/pow(10,$dp);
        } else {
            return floor($n*pow(10,$dp))/pow(10,$dp);
        }
    }
}