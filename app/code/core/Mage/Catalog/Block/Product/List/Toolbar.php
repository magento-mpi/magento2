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
    protected $_availableOrder      = array();
    protected $_availableMode       = array();
    protected $_enableViewSwitcher  = true;
    protected $_isExpanded          = true;

    public function __construct()
    {
        parent::__construct();
        $this->_availableOrder = array('position'=>__('Best Value'), 'name'=>'Name', 'price'=>__('Price'));
        
        switch (Mage::getStoreConfig('catalog/frontend/list_mode')) {
        	case 'grid':
		        $this->_availableMode = array('grid' => __('Grid'));
        		break;
        		
        	case 'list':
		        $this->_availableMode = array('list' => __('List'));
        		break;
        		
        	case 'grid-list':
		        $this->_availableMode = array('grid' => __('Grid'), 'list' => __('List'));
        		break;
        		
        	case 'list-grid':
		        $this->_availableMode = array('list' => __('List'), 'grid' => __('Grid'));
        		break;
        }
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
        $orders = $this->getAvailableOrders();
        if ($order && isset($orders[$order])) {
            return $order;
        }
        $keys = array_keys($orders);
        return $keys[0];
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
        if ($mode) {
            Mage::getSingleton('catalog/session')->setDisplayMode($mode);
        }
        else {
            $mode = Mage::getSingleton('catalog/session')->getDisplayMode();
        }
        
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

    public function disableViewSwitcher()
    {
        $this->_enableViewSwitcher = false;
        return $this;
    }

    public function enableViewSwitcher()
    {
        $this->_enableViewSwitcher = true;
        return $this;
    }

    public function isEnabledViewSwitcher()
    {
        return $this->_enableViewSwitcher;
    }

    public function disableExpanded()
    {
        $this->_isExpanded = false;
        return $this;
    }

    public function enableExpanded()
    {
        $this->_isExpanded = true;
        return $this;
    }

    public function isExpanded()
    {
        return $this->_isExpanded;
    }
    
    public function getAvailableLimit()
    {
        if ($this->getCurrentMode() == 'list') {
            return array(5=>5,10=>10,15=>15,20=>20,25=>25, 'all'=>__('All'));
        }
        elseif ($this->getCurrentMode() == 'grid') {
            return array(9=>9,15=>15,30=>30, 'all'=>__('All'));
        }
        return parent::getAvailableLimit();
    }
}
