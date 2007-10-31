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

/**
 * Adminhtml dashboard graph axis abstract
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
			$this->_collection = $this->getParentBlock()->getDataHelper()->getCollection();
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

	public function getLablesCount()
	{
	       return count($this->_labels);
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

		if($this->getCurrencyCode()) {
		    return Mage::app()->getLocale()->currency($this->getCurrencyCode())->toCurrency($value);
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

	public function getPixelPosition($item, $series)
	{
		return $series->getValue($item, $this);
	}

	public function getSpan()
	{
		return sizeof($this->getLabels()) + 2;
	}

	public function getPixelMaximum($item)
	{
		return 0;
	}
}// Class Mage_Adminhtml_Block_Graph_Axis_Abstract END