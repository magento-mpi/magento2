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
        return Mage::getSingleton('catalog_resource', 'product');
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
        $url = Mage::getUrl('catalog', 
            array(
                'controller'=>'product', 
                'action'=>'view', 
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
        $url = Mage::getUrl('catalog', array('controller'=>'category', 'action'=>'view', 'id'=>$this->getCategoryId()));
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
        return Mage::getModel('catalog_resource', 'category_tree')->joinAttribute('name')->loadNode($this->getCategoryId())->getName();
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
        $defaultCurrency = Mage::getSingleton('core', 'website')->getDefaultCurrency();
        $currentCurrency = Mage::getSingleton('core', 'website')->getCurrentCurrency();
        $price = $this->getTierPrice($qty);
        
        $canConvert = false;
        if ($defaultCurrency && $currentCurrency) {
            $filter     = $currentCurrency->getFilter();
            $canConvert = true;
        }
        elseif ($defaultCurrency) {
            $filter     = $defaultCurrency->getFilter();
            $canConvert = true;
            $currentCurrency = null;
        }
        else {
            $filter = new Varien_Filter_Sprintf('%s', 2);
        }
        
        if (is_array($price)) {
            foreach ($price as $index => $value) {
                if ($canConvert) {
                    $price[$index]['price'] = $filter->filter($defaultCurrency->convert($price[$index]['price'], $currentCurrency));
                }
                else {
                    $price[$index]['price'] = $filter->filter($price[$index]['price']);
                }
            }
        }
        else {
            if ($canConvert) {
                $price = $currentCurrency->filter($defaultCurrency->convert($price, $currentCurrency));
            }
            else {
                $price = $currentCurrency->filter($price);
            }
        }
        
        
        return $price;
    }

    public function getFormatedPrice()
    {
        $defaultCurrency = Mage::getSingleton('core', 'website')->getDefaultCurrency();
        $currentCurrency = Mage::getSingleton('core', 'website')->getCurrentCurrency();
        $price = $this->getPrice();
        
        if ($defaultCurrency && $currentCurrency) {
            $price = $currentCurrency->format($defaultCurrency->convert($price, $currentCurrency));
        }
        elseif ($defaultCurrency) {
            $price = $defaultCurrency->format($price);
        }
        else {
            $filter = new Varien_Filter_Sprintf('%s', 2);
            $price = $filter->filter($price);
        }
        
        
        return $price;
    }
    
    public function getLinkedProducts($linkType)
    {
        $linkedProducts = Mage::getModel('catalog_resource', 'product_link_collection');
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
        $categories = Mage::getModel('catalog_resource', 'category_collection')
            ->addProductFilter($this->getProductId())
            ->loadData();
            
        return $categories;
    }
}