<?php

/**
 * Category View block
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Category_View extends Mage_Catalog_Block_Product_List
{
    protected function _initChildren()
    {
        parent::_initChildren();
        
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbsBlock) {
            $path = $this->getCurrentCategory()->getPathInStore();
            $pathIds = array_reverse(explode(',', $path));
    
            $categories = Mage::getResourceModel('catalog/category_collection')
                ->addAttributeToSelect('name')
                ->addFieldToFilter('entity_id', array('in'=>$pathIds))
                ->load()
                ->getItems();
    
            // add category path breadcrumb
            foreach ($pathIds as $categoryId) {
                if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                    $breadcrumb = array(
                        'label' => $categories[$categoryId]->getName(),
                        'link'  => ($categories[$categoryId]->getId()==$this->getCurrentCategory()->getId())
                            ? '' : Mage::getUrl('*/*/*', array('id'=>$categories[$categoryId]->getId()))
                    );
                    $breadcrumbsBlock->addCrumb('category'.$categoryId, $breadcrumb);
                }
            }
        }
        
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->getCurrentCategory()->getName());
        }
        
        return $this;
    }

    /**
     * Retrieve current category model object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory()
    {
        return Mage::registry('current_category');
    }

    public function getCanShowName()
    {
        return $this->getCurrentCategory()->getDisplayMode()!=Mage_Catalog_Model_Category::DM_MIXED;
    }
}
