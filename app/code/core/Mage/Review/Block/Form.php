<?php
/**
 * Review form block
 *
 * @package     Mage
 * @subpackage  Review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Review_Block_Form extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        parent::__construct();
        
        $data =  Mage::getSingleton('review/session')->getFormData(true);
        if (!$data) {
            $data = new Varien_Object();
        }
        
        $productId = Mage::registry('controller')->getFront()->getRequest()->getParam('id', false);
        $this->setTemplate('review/form.phtml')
            ->assign('action', Mage::getUrl('review', array('controller'=>'product', 'action'=>'post', 'id'=>$productId)))
            ->assign('data', $data)
            ->assign('messages', Mage::getSingleton('review/session')->getMessages(true));
        
        $ratingCollection = Mage::getModel('rating/rating')->getCollection()
            ->addEntityFilter('product')
            ->setPositionOrder()
            ->load()
            ->addOptionToItems();
        
        $this->assign('ratings', $ratingCollection);
    }
}
