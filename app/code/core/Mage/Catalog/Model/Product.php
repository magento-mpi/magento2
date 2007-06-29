<?php
/**
 * Catalog product
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Product extends Varien_Object 
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
    
    /**
     * Get product id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getProductId();
    }
    
    /**
     * Get product category id
     *
     * @return int
     */
    public function getCategoryId()
    {
        $categoryId = ($this->getData('category_id')) ? $this->getData('category_id') : $this->getDefaultCategory();
        return $categoryId;
    }
    
    /**
     * Get product resource model
     *
     * @return mixed
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('catalog/product');
    }
    
    /**
     * Load product
     *
     * @param   int $productId
     * @return  Mage_Catalog_Model_Product
     */
    public function load($productId)
    {
        $this->setData($this->getResource()->load($productId));
        return $this;
    }
    
    /**
     * Save product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    /**
     * Get product url
     *
     * @return string
     */
    public function getProductUrl()
    {
        $url = Mage::getUrl('catalog/product/view', 
            array(
                'id'=>$this->getId(),
                'category'=>$this->getCategoryId()
            ));
        return $url;
    }
    
    /**
     * Get product category url
     *
     * @return string
     */
    public function getCategoryUrl()
    {
        $url = Mage::getUrl('catalog/category/view', array('id'=>$this->getCategoryId()));
        return $url;
    }
    
    public function getImageUrl()
    {
        #$url = Mage::getBaseUrl(array('_admin'=>false, '_type'=>'media')).'catalog/product/'.($this->getProductId()%977).'/'.$this->getProductId().'.orig.'.$this->getImage();
        $url = Mage::getBaseUrl(array('_admin'=>false, '_type'=>'media')).$this->getImage();
        return $url;
    }
        
    public function getSmallImageUrl()
    {
        #$url = Mage::getBaseUrl(array('_admin'=>false, '_type'=>'media')).'catalog/product/'.($this->getProductId()%977).'/'.$this->getProductId().'.orig.'.$this->getImage();
        $url = Mage::getBaseUrl(array('_admin'=>false, '_type'=>'media')).$this->getSmallImage();
        return $url;
    }
    
    /**
     * Get product category name
     *
     * @return unknown
     */
    public function getCategoryName()
    {
        return Mage::getResourceModel('catalog/category_tree')->joinAttribute('name')->loadNode($this->getCategoryId())->getName();
    }
    
    /**
     * Get product tier price by qty
     *
     * @param   double $qty
     * @return  double
     */
    public function getTierPrice($qty=null)
    {
        $prices = $this->getData('tier_price');
        
        if (empty($prices) || !is_array($prices)) {
            if (!is_null($qty)) {
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
    
    /**
     * Get formated by currency tier price
     *
     * @param   double $qty
     * @return  array || double
     */
    public function getFormatedTierPrice($qty=null)
    {
        $price = $this->getTierPrice($qty);
        if (is_array($price)) {
            foreach ($price as $index => $value) {
                $price[$index]['price'] = Mage::getSingleton('core/store')->formatPrice($price[$index]['price']);
            }
        }
        else {
            $price = Mage::getSingleton('core/store')->formatPrice($price);
        }
        
        
        return $price;
    }

    public function getFormatedPrice()
    {
        return Mage::getSingleton('core/store')->formatPrice($this->getPrice());
    }
    
    public function getLinkedProducts($linkType)
    {
        $linkedProducts = Mage::getResourceModel('catalog/product_link_collection');
        $linkedProducts->getProductCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('description');
        

        $linkedProducts->addProductFilter($this->getProductId())
            ->addTypeFilter($linkType)
            ->loadData();
            
        return $linkedProducts;
    }
    
    public function getRelatedProducts()
    {
        return $this->getLinkedProducts(1);
    }
    
    public function getCategories()
    {
        $categories = Mage::getResourceModel('catalog/category_collection')
            ->addProductFilter($this->getProductId())
            ->loadData();
            
        return $categories;
    }
}