<?php
/**
 * Category View block
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Block_Category_View extends Mage_Core_Block_Template
{
    protected function _initChildren()
    {
        parent::_initChildren();

        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbsBlock) {
    	    $breadcrumbsBlock->addCrumb('home',
                array('label'=>__('Home'), 'title'=>__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));

            $path = $this->getCurrentCategory()->getPathInStore();
            $pathIds = array_reverse(explode(',', $path));

            $categories = Mage::getResourceModel('catalog/category_collection')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('url_key')
                ->addFieldToFilter('entity_id', array('in'=>$pathIds))
                ->load()
                ->getItems();

            // add category path breadcrumb
            foreach ($pathIds as $categoryId) {
                if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                    $breadcrumb = array(
                        'label' => $categories[$categoryId]->getName(),
                        'link'  => $categoryId==$this->getCurrentCategory()->getId()
                            ? '' : $categories[$categoryId]->getCategoryUrl()
                    );
                    $breadcrumbsBlock->addCrumb('category'.$categoryId, $breadcrumb);
                }
            }
        }

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->getCurrentCategory()->getName());
        }

        if ($layout = $this->getCurrentCategory()->getPageLayout()) {
            $template = (string)Mage::getConfig()->getNode('global/cms/layouts/'.$layout.'/template');
            $this->getLayout()->getBlock('root')->setTemplate($template);
        }

        return $this;
    }

    public function getProductListHtml()
    {
        return $this->getChildHtml('product_list');
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

    public function getLandingPageBlock()
    {
        $block = $this->getData('landing_page_block');
        if (is_null($block)) {
            $block = Mage::getModel('cms/block')
                ->load($this->getCurrentCategory()->getLandingPage());
            $this->setData('landing_page_block', $block);
        }
        return $block;
    }

    public function isProductMode()
    {
        return $this->getCurrentCategory()->getDisplayMode()==Mage_Catalog_Model_Category::DM_PRODUCT;
    }

    public function isMixedMode()
    {
        return $this->getCurrentCategory()->getDisplayMode()==Mage_Catalog_Model_Category::DM_MIXED;
    }

    public function isContentMode()
    {
        return $this->getCurrentCategory()->getDisplayMode()==Mage_Catalog_Model_Category::DM_PAGE;
    }
}
