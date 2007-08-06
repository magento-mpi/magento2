<?php
/**
 * Layer category filter abstract model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
abstract class Mage_Catalog_Model_Layer_Filter_Abstract extends Varien_Object
{
    /**
     * Request variable name with filter value
     *
     * @var string
     */
    protected $_requestVar;
    
    /**
     * Array of filter items
     *
     * @var array
     */
    protected $_items;
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function setRequestVar($varName)
    {
        $this->_requestVar = $varName;
        return $this;
    }
    
    public function getRequestVar()
    {
        return $this->_requestVar;
    }
    
    /**
     * Apply filter to collection
     *
     * @param  Zend_Controller_Request_Abstract $request
     */
    public function apply(Zend_Controller_Request_Abstract $request) 
    {
        
    }
    
    public function getItemsCount()
    {
        return count($this->getItems());
    }
    
    public function getItems()
    {
        if (is_null($this->_items)) {
            $this->_initItems();
        }
        return $this->_items;
    }
    
    protected function _initItems()
    {
        $this->_items = array();
        return $this;
    }
}
