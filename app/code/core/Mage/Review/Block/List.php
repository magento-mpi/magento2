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
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('review/list.phtml');
        
        $productId = Mage::registry('controller')->getFront()->getRequest()->getParam('id', false);
        
        $collection = Mage::getModel('review/review')->getCollection();
        $collection->setPageSize(10)
            ->addWebsiteFilter(Mage::getSingleton('core/website')->getId())
            ->addStatusFilter('approved')
            ->addEntityFilter('product', $productId)
            ->setDateOrder()
            ->load();
            
        $this->assign('collection', $collection);
    }
}
