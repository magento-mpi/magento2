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

        $this->setTemplate('review/form.phtml')
            ->assign('data', $data)
            ->assign('messages', Mage::getSingleton('review/session')->getMessages(true));
    }

    public function getProductInfo()
    {
        $product = Mage::getModel('catalog/product');
        return $product->load($this->getRequest()->getParam('id'));
    }

    public function getAction()
    {
        $productId = Mage::registry('controller')->getRequest()->getParam('id', false);
        return Mage::getUrl('review/product/post', array('id' => $productId));
    }

    public function getRatings()
    {
        $ratingCollection = Mage::getModel('rating/rating')
            ->getResourceCollection()
            ->addEntityFilter('product')
            ->setPositionOrder()
            ->load()
            ->addOptionToItems();
        return $ratingCollection;
    }
}
