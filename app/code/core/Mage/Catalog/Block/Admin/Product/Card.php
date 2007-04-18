<?php
/**
 * Product attributes form
 *
 * @package    Mage_Admin
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Admin_Product_Card extends Mage_Core_Block_Abstract
{
    public function toJson()
    {
        $productId      = (int) Mage::registry('controller')->getRequest()->getParam('product', false);
        $attributeSetId = (int) Mage::registry('controller')->getRequest()->getParam('setid', false);
        
        // 
        if ($productId) {
            $product = Mage::getModel('catalog', 'product')->load($productId);
            $set     = Mage::getModel('catalog', 'product_attribute_set')->load($product->getSetId());
        }
        else {
            if (!$attributeSetId) {
                $setCollection  = Mage::getModel('catalog_resource', 'product_attribute_set_collection')->load();
                if ($setCollection->getSize()) {
                    $set = $setCollection->getFirstItem();
                }
                else {
                    Mage::exception('Undefined attributes set id');
                }
            }
            else {
                $set = Mage::getModel('catalog', 'product_attribute_set')->load($attributeSetId);
            }
        }
        
        
        $groups = $set->getGroups();
        
        // Create card JSON structure
        $cardStructure = array();
        //$cardStructure['attribute_set'] = $arrSets;
        $cardStructure['tabs'] = array();
        
        // Tabs description JSON
        $baseTabUrl = Mage::getBaseUrl().'/mage_catalog/product/form/';
        if ($productId) {
            $baseTabUrl.= 'product/' . $productId . '/';
        }
        
        foreach ($groups as $group) {
            $url = $baseTabUrl . 'group/' . $group->getId().'/';
            $url.= 'set/'.$set->getId().'/';
            $cardStructure['tabs'][] = array(
                'name'  => $group->getCode(),
                'url'   => $url,
                'title' => $group->getCode(),
            );
        }

        $cardStructure['tabs'][] = array(
            'name'  => 'related',
            'type'  => 'related',
            'url'   => Mage::getBaseUrl().'/mage_catalog/product/relatedProducts/',
            'title' => 'Related products',
        );
        
        // Set first tab as active
        $cardStructure['tabs'][0]['active'] = true;
        $cardStructure['tabs'][0]['url']    = $cardStructure['tabs'][0]['url'] . 'isdefault/1/';
        return Zend_Json::encode($cardStructure);
    }
}