<?php
/**
 * Product attributes form
 *
 * @package    Mage
 * @subpackage Admin
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 */
class Mage_Admin_Block_Catalog_Product_Card extends Mage_Core_Block_Abstract
{
    /**
     * Get json string describing admin product card panel
     *
     * @param array $arrAttributes
     * @return string
     */
    public function toJson(array $arrAttributes = array())
    {
        $productId = (int) Mage::registry('controller')->getRequest()->getParam('product', false);
        if ($productId<0) {
            $productId = false;
        }
        
        $setId  = (int) Mage::registry('controller')->getRequest()->getParam('setid', false);
        $typeId = Mage::registry('controller')->getRequest()->getParam('type', false);
        
        if ($productId) {
            $product = Mage::getModel('catalog', 'product')->load($productId);
            $setId   = $product->getSetId();
            $typeId  = $product->getTypeId();
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
        if ($productId) {
            $cardStructure['title'] = $product->getName() . ' #' . $product->getId(); 
        }
        else {
            $cardStructure['title'] = __('New Product'); 
        }
        
        $cardStructure['tabs']  = array();
        $cardStructure['saveUrl'] = Mage::getUrl('admin', 
            array(
                'controller'=> 'product', 
                'action'    => 'save',
                'product'   => $productId,
                'set'       => $setId,
                'type'      => $typeId
            )
        );
        
        // Product information tab
        if ($productId) {
            $cardStructure['tabs'][] = array(
                'name'  => 'product_view',
                'url'   => Mage::getUrl('admin', array('controller'=>'product', 'action'=>'view')).$urlPrefix,
                'title' => 'Product Info',
                'type'  => 'view'
            );
        }
        
        // Attributes group tabs
        foreach ($groups as $group) {
            $cardStructure['tabs'][] = array(
                'name'  => $group->getCode(),
                'title' => $group->getCode(),
                'type'  => 'form',
                'background' => true,
                'form'  => Mage::getSingleton('core', 'layout')
                            ->createBlock('admin_product_form', 'p_form_'.$group->getCode())
                                ->setGroupId($group->getId())
                                ->render()
                                ->toArray()
            );
        }

        if ($productId) {
            $imagesForm = new Varien_Data_Form();
            $imagesForm->setAction(Mage::getBaseUrl()."admin/product/upload/".$urlPrefix)
                ->setMethod('post')
                ->setFileupload(true)
                ->addField('image', 'file', array('name'=>'image', 'label'=>'Image file', 'autoSubmit'=>true));
                
            $cardStructure['tabs'][] = array(
                'name'  => 'images',
                'type'  => 'images',
                'storeUrl' => Mage::getBaseUrl()."admin/product/imageCollection/".$urlPrefix,
                'title' => 'Images',
                'form'  => $imagesForm->toArray()
            );
            
            $cardStructure['tabs'][] = array(
                'name'  => 'categories',
                'type'  => 'categories',
                'saveVar'   => 'categories[]',
                'storeUrl'  => Mage::getBaseUrl()."admin/product/categoryList/".$urlPrefix,
                'title' => 'Categories',
            );
        }
        
        // TODO: detect by $typeId
        /*if ($productType && $productType != 'default') {
            $cardStructure['tabs'][] = array(
                'name'  => $productType,
                'type'  => $productType,
                'saveVar' => $productType,
                'url'   => Mage::getBaseUrl().'admin/product/'.$productType.'Products/'.$urlPrefix,
                'title' => $productType,
            );
        }*/
        
        $cardStructure['tabs'][] = array(
            'name'  => 'related',
            'type'  => 'related',
            'saveVar' => 'related[]',
            'url'   => Mage::getBaseUrl().'admin/product/relatedTab/' . $urlPrefix,
            'title' => 'Related products',
        );
        
        // Set first tab as active
        $cardStructure['tabs'][0]['active'] = true;
        return Zend_Json::encode($cardStructure);
    }
}
