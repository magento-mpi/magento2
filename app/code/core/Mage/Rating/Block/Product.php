<?php
/**
 * Product rating block
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Rating_Block_Product extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('rating/product.phtml');

        $productId = Mage::registry('action')->getRequest()->getParam('id');
        $ratingCollection = Mage::getModel('rating/rating')
            ->getResourceCollection()
            ->addEntityFilter('product')
            ->setPositionOrder()
            ->load();

        if ($productId) {
            $ratingCollection->addEntitySummaryToItem($productId);
        }

        $this->assign('collection', $ratingCollection);
    }
}