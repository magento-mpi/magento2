<?php
/**
 * Product gallery
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Block_Product_Gallery extends Mage_Core_Block_Template
{
    protected function _initChildren()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->getProduct()->getMetaTitle());
        }
        return parent::_initChildren();
    }
    public function getProduct()
    {
        return Mage::registry('product');
    }
    
    public function getGalleryCollection()
    {
        return $this->getProduct()->getGallery();
    }
    
    public function getCurrentImage()
    {
        $imageId = $this->getRequest()->getParam('image');
        $image = null;
        if ($imageId) {
            $image = $this->getGalleryCollection()->getItemById($imageId);
        }
        
        if (!$image) {
            $image = $this->getGalleryCollection()->getFirstItem();
        }
        return $image;
    }
    
    public function getPreviusImage()
    {
        $current = $this->getCurrentImage();
        if (!$current) {
            return false;
        }
        $previus = false;
        foreach ($this->getGalleryCollection() as $image) {
            if ($image->getValueId() == $current->getValueId()) {
        	    return $previus;
        	}
        	$previus = $image;
        }
        return $previus;
    }
    
    public function getNextImage()
    {
        $current = $this->getCurrentImage();
        if (!$current) {
            return false;
        }
        
        $next = false;
        $currentFind = false;
        foreach ($this->getGalleryCollection() as $image) {
            if ($currentFind) {
                return $image;
            }
        	if ($image->getValueId() == $current->getValueId()) {
        	    $currentFind = true;
        	}
        }
        return $next;
    }
    
    public function getPreviusImageUrl()
    {
        if ($image = $this->getPreviusImage()) {
            return $this->getUrl('*/*/*', array('_current'=>true, 'image'=>$image->getValueId()));
        }
        return false;
    }

    public function getNextImageUrl()
    {
        if ($image = $this->getNextImage()) {
            return $this->getUrl('*/*/*', array('_current'=>true, 'image'=>$image->getValueId()));
        }
        return false;
    }
}
