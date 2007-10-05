<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Catalog category helper
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Helper_Product extends Mage_Core_Helper_Url
{
    /**
     * Retrieve product view page url
     *
     * @param   mixed $product
     * @return  string
     */
    public function getProductUrl($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $urlKey = $product->getUrlKey() ? $product->getUrlKey() : $product->getName();
            $params = array(
                's'         => $this->_prepareString($urlKey),
                'id'        => $product->getId(),
                'category'  => $product->getCategoryId()
            );
            return $this->_getUrl('catalog/product/view', $params);
        }
        if ((int) $product) {
            return $this->_getUrl('catalog/product/view', array('id'=>$product));
        }
        return false;
    }
    
    public function getImageUrl()
    {
        
    }
    
    public function getSmallImageUrl()
    {
        
    }
    
    public function getThumbnailUrl()
    {
        
    }
}
