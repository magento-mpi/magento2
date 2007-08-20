<?php
/**
 * Product list toolbar
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Block_Product_List_Toolbar extends Mage_Page_Block_Html_Pager
{
    protected $_orderVarName        = 'order';
    protected $_directionVarName    = 'dir';
    protected $_modeVarName         = 'mode';
    protected $_availableOrder      = array('price', 'name');
    protected $_availableMode       = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->_availableMode = array('grid' => __('Grid'), 'list' => __('List'));
        $this->setTemplate('catalog/product/list/toolbar.phtml');
    }
    
    public function setCollection($collection)
    {
        parent::setCollection($collection);
        if ($this->getCurrentOrder()) {
            $this->getCollection()->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
        }
        return $this;
    }
    
    public function getOrderVarName()
    {
        return $this->_orderVarName;
    }
    
    public function getDirectionVarName()
    {
        return $this->_directionVarName;
    }

    public function getModeVarName()
    {
        return $this->_modeVarName;
    }
    
    public function getCurrentOrder()
    {
        $order = $this->getRequest()->getParam($this->getOrderVarName());
        if ($order && in_array($order, $this->getAvailableOrders())) {
            return $order;
        }
        return false;
    }
    
    public function getCurrentDirection()
    {
        if ($dir = (string) $this->getRequest()->getParam($this->getDirectionVarName())) {
            $dir = strtolower($dir);
            if (in_array($dir, array('asc', 'desc'))) {
                return $dir;
            }
        }
        return 'asc';
    }
    
    public function getAvailableOrders()
    {
        return $this->_availableOrder;
    }
    
    public function isOrderCurrent($order)
    {
        return $order == $this->getRequest()->getParam('order');
    }
    
    public function getOrderUrl($order, $direction)
    {
        if (is_null($order)) {
            $order = $this->getCurrentOrder() ? $this->getCurrentOrder() : $this->_availableOrder[0];
        }
        return $this->getPagerUrl(array(
            $this->getOrderVarName()=>$order, 
            $this->getDirectionVarName()=>$direction
        ));
    }
    
    public function getCurrentMode()
    {
        $mode = $this->getRequest()->getParam($this->getModeVarName());
        if ($mode && isset($this->_availableMode[$mode])) {
            return $mode;
        }
        return current(array_keys($this->_availableMode));
    }
    
    public function isModeActive($mode)
    {
        return $this->getCurrentMode() == $mode;
    }
    
    public function getModes()
    {
        return $this->_availableMode;
    }
    
    public function getModeUrl($mode)
    {
        return $this->getPagerUrl(array($this->getModeVarName()=>$mode));
    }
}
