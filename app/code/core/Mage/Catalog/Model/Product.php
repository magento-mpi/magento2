<?php
/**
 * Catalog product
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Catalog_Model_Product extends Varien_Data_Object 
{
    /**
     * Product attributes information
     *
     * @var array
     */
    protected $_attributes;
    
    /**
     * Product set information
     *
     * @var array
     */
    protected $_set;
    
    /**
     * Group 
     *
     * @var unknown_type
     */
    protected $_group;
    
    /**
     * Use multiple values flag
     *
     * @var bool
     */
    protected $_useMultiple;
    
    public function __construct($product=false) 
    {
        parent::__construct();
        
        if (is_numeric($product)) {
            $this->load($product);
        }
        elseif (is_array($product)) {
            $this->setData($product);
        }
    }
    
    public function load($productId)
    {
        $this->setProductId($productId);
    }
    
    public function save()
    {
        // save product
    }
    
    public function getAttributes()
    {
        if ($this->_attributes) {
            return $this->_attributes;
        }
    }
    
    public function getLink()
    {
        $url = Mage::getBaseUrl().'/catalog/product/view/id/'.$this->getProductId();
        return $url;
    }
    
    public function getCategoryLink()
    {
        // TODO : default category id attribute
        $url = Mage::getBaseUrl().'/catalog/category/view/id/3';
        return $url;
    }
    
    public function getCategoryName()
    {
        // TODO : default category id attribute
        $category = Mage::getModel('catalog', 'category_tree')->getNode(3);
        return $category->getData('attribute_value');
    }
    
    public function getLargeImageLink()
    {
        return Mage::getBaseUrl().'/catalog/product/image/id/'.$this->getProductId();
    }
    
    public function getTierPrice($qty=1)
    {
        return $this->getPrice();
    }
}