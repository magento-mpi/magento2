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
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) 
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
    
    /**
     * Retrieve layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        $layer = $this->getData('layer');
        
        if (is_null($layer)) {
            $layer = Mage::getSingleton('catalog/layer');
            $this->setData('layer', $layer);
        }
        
        return $layer;
    }
    
    /**
     * Create filter item object
     *
     * @param   string $label
     * @param   mixed $value
     * @param   int $count
     * @return  Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function _createItem($label, $value, $count=0)
    {
        return Mage::getModel('catalog/layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count);
    }
}
