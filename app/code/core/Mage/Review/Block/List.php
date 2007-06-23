<?php
/**
 * Review list block
 *
 * @package     Mage
 * @subpackage  Review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Review_Block_List extends Mage_Core_Block_Template 
{
    protected $_collection;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('review/list.phtml');
        $productId = Mage::registry('controller')->getRequest()->getParam('id', false);
        
        $this->_collection = Mage::getModel('review/review')->getCollection();
        $this->_collection->setPageSize(10)
            ->addStoreFilter(Mage::getSingleton('core/store')->getId())
            ->addStatusFilter('approved')
            ->addEntityFilter('product', $productId)
            ->setDateOrder();
    }
    
    public function count()
    {
        return $this->_collection->getSize();
    }
    
    public function toHtml()
    {
        $request    = Mage::registry('controller')->getRequest();
        $productId  = $request->getParam('id', false);
        $page       = $request->getParam('p',1);
        
        $this->_collection->setCurPage($page)
            ->load();
        $this->assign('collection', $this->_collection);
        
        $backUrl = Mage::getUrl('catalog', 
            array(
                'controller'=>'product', 
                'action'=>'view', 
                'id'=>$productId,
            )
        );
        $this->assign('backLink', $backUrl);
        
        $pageUrl = clone $request;
        $this->assign('pageUrl', $pageUrl);
       
        return parent::toHtml();
    }
}
