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
        $categoryId = ($this->getData('category_id')) ? $this->getData('category_id') : $this->getDefaultCategory();
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
    
    public function getProductUrl()
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
    
    public function getCategoryUrl()
    {
        $url = Mage::getUrl('catalog', array('controller'=>'category', 'action'=>'view', 'id'=>$this->getCategoryId()));
        return $url;
    }
    
    public function getImageUrl()
    {
        $url = Mage::getBaseUrl(array('_admin'=>false, '_type'=>'media')).'catalog/product/'.($this->getProductId()%977).'/'.$this->getProductId().'.orig.'.$this->getImage();
        return $url;
    }
    
    public function getCategoryName()
    {
        return Mage::getModel('catalog_resource', 'category_tree')->joinAttribute('name')->loadNode($this->getCategoryId())->getName();
    }
    /*
    public function getPrice()
    {
        $price = $this->getData('price');
        if (is_array($price) && isset($price[0])) {
            return $price[0]['price'];
        }
        elseif(is_numeric($price)) {
            return $price;
        }
        return null;
    }*/
    
    public function getTierPrice($qty=null)
    {
        $prices = $this->getData('tier_price');
        if (empty($prices) || !is_array($prices)) {
            if ($qty) {
                return $this->getPrice();
            }
            return array(array('price'=>$this->getPrice(), 'price_qty'=>1));
        }
        if ($qty) {
            $prevQty = 0;
            $prevPrice = $prices[0]['price'];
            foreach ($prices as $price) {
                if (($prevQty <= $qty) && ($qty < $price['price_qty'])) {
                    return $prevPrice;
                }
                $prevPrice = $price['price'];
                $prevQty = $price['price_qty'];
            }
            return $prevPrice;
        }
        
        return ($prices) ? $prices : array();
    }

    public function getFormatedPrice()
    {
        $filter = new Varien_Filter_Sprintf('$%s', 2);
        return $filter->filter($this->getPrice());
    }
    
    public function getLinkedProducts($linkType)
    {
        $linkedProducts = Mage::getModel('catalog_resource', 'product_link_collection')
            ->addProductFilter($this->getProductId())
            ->addTypeFilter($linkType)
            ->loadData();
            
        return $linkedProducts;
    }
    
    public function getCategories()
    {
        $categories = Mage::getModel('catalog_resource', 'category_collection')
            ->addProductFilter($this->getProductId())
            ->loadData();
            
        return $categories;
    }
}