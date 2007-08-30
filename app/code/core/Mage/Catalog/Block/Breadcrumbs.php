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
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Catalog breadcrumbs
 *
 * @package     Mage
 * @subpackage  Mage_Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Block_Breadcrumbs extends Mage_Core_Block_Template
{
    protected function _initChildren()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home',
                array('label'=>__('Home'), 'title'=>__('Go to Home Page'), 'link'=>Mage::getBaseUrl())
            );
            
            if ($this->getCategory()) {
                $path = $this->getCategory()->getPathInStore();
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
                            'link'  => $this->_isCategoryLink($categoryId) ? $categories[$categoryId]->getCategoryUrl() : ''
                        );
                        $breadcrumbsBlock->addCrumb('category'.$categoryId, $breadcrumb);
                    }
                }
            }
            
            if ($this->getProduct()) {
                $breadcrumbsBlock->addCrumb('product', array('label'=>$this->getProduct()->getName()));
            }
        }
    }
    
    protected function _isCategoryLink($categoryId)
    {
        if ($this->getProduct()) {
            return true;
        }
        if ($categoryId != $this->getCategory()->getId()) {
            return true;
        }
        return false;
    }
    
    public function getCategory()
    {
        return Mage::registry('current_category');
    }
    
    public function getProduct()
    {
        return Mage::registry('current_product');
    }
}
