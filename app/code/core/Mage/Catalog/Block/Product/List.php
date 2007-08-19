<?php
/**
 * Product list
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Block_Product_List extends Mage_Core_Block_Template 
{
    protected $_productCollection;

    protected function _initChildren()
    {
        $pager = $this->getLayout()->createBlock('page/html_pager', 'pager')
            ->setCollection($this->_getProductCollection());
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'pager')
            ->setCollection($this->_getProductCollection());
            
        $this->setChild('pager', $pager);

        // add Home breadcrumb
    	if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
    	    $breadcrumbBlock->addCrumb('home',
                array('label'=>__('Home'), 'title'=>__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
    	}
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        return Mage::getSingleton('catalog/layer')->getProductCollection();
    }
    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }
    
    /**
     * Retrieve collection pager HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getRequest()->getParam('mode');
    }
    
    protected function _beforeToHtml()
    {
        $this->_getProductCollection()->load();
        Mage::getModel('review/review')->appendSummary($this->_getProductCollection());
        return parent::_beforeToHtml();
    }
}