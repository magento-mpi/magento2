<?php
/**
 * Product attributes form
 *
 * @package    Mage_Admin
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 */
class Mage_Catalog_Block_Admin_Product_Card extends Mage_Core_Block_Abstract
{
    /**
     * Get json string describing admin product card panel
     *
     * @param array $arrAttributes
     * @return string
     */
    public function toJson(array $arrAttributes = array())
    {
        $productId      = (int) Mage::registry('controller')->getRequest()->getParam('product', false);
        if ($productId<0) {
            $productId = false;
        }
        $attributeSetId = (int) Mage::registry('controller')->getRequest()->getParam('setid', false);
        $productType    = Mage::registry('controller')->getRequest()->getParam('type', false);
        
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
        $urlPrefix = ($productId) ? 'product/'.$productId . '/' : '';
        
        $groups = $set->getGroups();
        
        // Create card JSON structure
        $cardStructure = array();
        //$cardStructure['attribute_set'] = $arrSets;
        $cardStructure['tabs'] = array();
        
        if ($productId) {
            $cardStructure['tabs'][] = array(
                'name'  => 'product_view',
                'url'   => Mage::getBaseUrl()."mage_catalog/product/view/".$urlPrefix,
                'title' => 'Product Info',
                'type'  => 'view'
            );
        }
        
        // Tabs description JSON
        $baseTabUrl = Mage::getBaseUrl().'mage_catalog/product/form/';
        foreach ($groups as $group) {
            $url = $baseTabUrl . 'group/' . $group->getId().'/' . $urlPrefix;
            $url.= 'set/'.$set->getId().'/';
            
            Mage::registry('controller')->getRequest()->setParam('group', $group->getId());
            $cardStructure['tabs'][] = array(
                'name'  => $group->getCode(),
                'background' => true,
                'title' => $group->getCode(),
                'type'  => 'form',
                'form'  => Mage::getSingleton('core', 'layout')->createBlock('admin_catalog_product_form_json', 'product_form')
                            ->toArray()
            );
        }

        if ($productId) {
            $imagesForm = new Varien_Data_Form();
            $imagesForm->setAction(Mage::getBaseUrl()."mage_catalog/product/upload/".$urlPrefix)
                ->setMethod('post')
                ->setFileupload(true)
                ->addField('image', 'file', array('name'=>'image', 'label'=>'Image file', 'autoSubmit'=>true));
                
            $cardStructure['tabs'][] = array(
                'name'  => 'images',
                'type'  => 'images',
                //'saveUrl' => Mage::getBaseUrl()."mage_catalog/product/upload/".$urlPrefix,
                //'url'   => Mage::getBaseUrl()."mage_catalog/product/images/".$urlPrefix,
                'storeUrl' => Mage::getBaseUrl()."mage_catalog/product/imageCollection/".$urlPrefix,
                'title' => 'Images',
                'form'  => $imagesForm->toArray()
            );
            
            $cardStructure['tabs'][] = array(
                'name'  => 'categories',
                'type'  => 'categories',
                'storeUrl'   => Mage::getBaseUrl()."mage_catalog/product/categoryList/".$urlPrefix,
                'title' => 'Categories',
            );
        }
        
        if ($productType && $productType != 'default') {
            $cardStructure['tabs'][] = array(
                'name'  => $productType,
                'type'  => $productType,
                'url'   => Mage::getBaseUrl().'mage_catalog/product/'.$productType.'Products/'.$urlPrefix,
                'title' => $productType,
            );
        }
        
        $cardStructure['tabs'][] = array(
            'name'  => 'related',
            'type'  => 'related',
            'url'   => Mage::getBaseUrl().'mage_catalog/product/relatedTab/' . $urlPrefix,
            'title' => 'Related products',
        );
        
        // Set first tab as active
        $cardStructure['tabs'][0]['active'] = true;
        return Zend_Json::encode($cardStructure);
    }
}
