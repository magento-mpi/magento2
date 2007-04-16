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

        $setCollection  = Mage::getModel('catalog_resource', 'product_attribute_set_collection');
        $setCollection->load();
        $arrSets = $setCollection->__toArray();
        
        if ($productId) {
            $productModel   = Mage::getModel('catalog_resource', 'product');
            $attributeSetId = $productModel->getAttributeSetId($productId);
        }
        
        // Get first sttributes set id
        if (!$attributeSetId) {
            if (isset($arrSets['items'][0])) {
                $attributeSetId = $arrSets['items'][0]['product_attribute_set_id'];
            }
            else {
                Mage::exception('Undefined attributes set id');
            }
        }
        
        // Declare set attributes
        $set = Mage::getModel('catalog_resource', 'product_attribute_set');
        $arrGroups = $set->getGroups($attributeSetId);
        
        // Create card JSON structure
        $cardStructure = array();
        $cardStructure['attribute_set'] = $arrSets;
        $cardStructure['tabs'] = array();
        
        // Tabs description JSON
        $baseTabUrl = Mage::getBaseUrl().'/mage_catalog/product/form/';
        if ($productId) {
            $baseTabUrl.= 'product/' . $productId . '/';
        }
        
        foreach ($arrGroups as $group) {
            $url = $baseTabUrl . 'group/' . $group['product_attribute_group_id'].'/';
            $url.= 'set/'.$attributeSetId.'/';
            $cardStructure['tabs'][] = array(
                'name'  => $group['product_attribute_group_code'],
                'url'   => $url,
                'title' => $group['product_attribute_group_code'],
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