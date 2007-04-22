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
    
    public function getCategoryId()
    {
        $categoryId = ($this->getData('category_id')) ? $this->getData('category_id') : $this->getDefaultCategoryId();
        return $categoryId;
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
        $url = Mage::getUrl('catalog', 
            array(
                'controller'=>'product', 
                'action'=>'view', 
                'id'=>$this->getId(),
                'category'=>$this->getCategoryId()
            ));
        return $url;
    }
    
    public function getCategoryLink()
    {
        $url = Mage::getUrl('catalog', array('controller'=>'category', 'action'=>'view', 'id'=>$this->getCategoryId()));
        return $url;
    }
    
    public function getCategoryName()
    {
        return Mage::getModel('catalog_resource', 'category_tree')->joinAttribute('name')->loadNode($this->getCategoryId())->getName();
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