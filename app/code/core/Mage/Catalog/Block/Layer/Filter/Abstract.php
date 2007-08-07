<?php
/**
 * Catalog layer filter abstract
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
abstract class Mage_Catalog_Block_Layer_Filter_Abstract extends Mage_Core_Block_Template
{
    protected $_filter;
    protected $_filterModelName;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('catalog/layer/filter.phtml');
    }

    public function init()
    {
        $this->_initFilter();
        return $this;
    }
    
    /**
     * Init filter model object
     *
     * @return Mage_Catalog_Block_Layer_Filter_Abstract
     */
    protected function _initFilter()
    {
        if (!$this->_filterModelName) {
            Mage::throwException('Filter model name must be declared');
        }
        $this->_filter = Mage::getModel($this->_filterModelName);
        $this->_prepareFilter();
        
        $this->_filter->apply($this->getRequest(), $this);
        return $this;
    }
    
    protected function _prepareFilter()
    {
        return $this;
    }
    
    /**
     * Retrieve name of the filter block
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_filter->getName();
    }
    
    /**
     * Retrieve filter items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_filter->getItems();
    }
    
    /**
     * Retrieve filter items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        return $this->_filter->getItemsCount();
    }
    
    /**
     * Retrieve block html
     *
     * @return string
     */
    public function getHtml()
    {
        return parent::toHtml();
    }
}