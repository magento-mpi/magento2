<?php
/**
 * Catalog product
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Product extends Varien_Data_Object 
{
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
    
    public function getId()
    {
        return $this->getProductId();
    }
    
    public function getResource()
    {
        static $resource;
        if (!$resource) {
            $resource = Mage::getModel('catalog_resource', 'product');
        }
        return $resource;
    }
    
    public function load($productId)
    {
        $this->setData($this->getResource()->load($productId));
        return $this;
    }
    
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    public function getLink()
    {
        $url = Mage::getUrl('catalog', array('controller'=>'product', 'action'=>'view', 'id'=>$this->getId()));
        return $url;
    }
    
    public function getCategoryLink()
    {
        $url = Mage::getUrl('catalog', array('controller'=>'category', 'action'=>'view', 'id'=>$this->getDefaultCategoryId()));
        return $url;
    }
    
    public function getCategoryName()
    {
        // TODO : default category id attribute
        $category = Mage::getModel('catalog', 'category_tree')->getNode(3);
        return $category->getData('attribute_value');
    }
    
    public function getTierPrice($qty=1)
    {
        return $this->getPrice();
    }

    public function getFormatedPrice()
    {
        $filter = new Varien_Filter_Sprintf('$%s', 2);
        return $filter->filter($this->getPrice());
    }
}