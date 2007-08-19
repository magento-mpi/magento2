<?php
/**
 * Review controller
 *
 * @package     Mage
 * @subpackage  Review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Review_ProductController extends Mage_Core_Controller_Front_Action
{
    public function postAction()
    {
        $url = $this->getRequest()->getServer('HTTP_REFERER', Mage::getBaseUrl());

        $productId = $this->getRequest()->getParam('id', false);
        if ($data = $this->getRequest()->getPost()) {
            $review = Mage::getModel('review/review')->setData($data);
            try {
                $review->setEntityId(1) // product
                    ->setEntityPkValue($productId)
                    ->setStatusId(2) // pending
                    ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                    ->setStoreId(Mage::getSingleton('core/store')->getId())
                    ->save();

                $arrRatingId = $this->getRequest()->getParam('ratings', array());
                foreach ($arrRatingId as $ratingId=>$optionId) {
                	Mage::getModel('rating/rating')
                	   ->setRatingId($ratingId)
                	   ->setReviewId($review->getId())
                	   ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                	   ->addOptionVote($optionId, $productId);
                }

                $review->aggregate();

                Mage::getSingleton('review/session')->addMessage(
                    Mage::getModel('core/message')->success('Your review added')
                );
            }
            catch (Exception $e){
                Mage::getSingleton('review/session')->addMessage(
                    Mage::getModel('core/message')->error('Add review error')
                );
            }
        }

        $this->getResponse()->setRedirect($url);
    }

    public function listAction()
    {
        $productId = $this->getRequest()->getParam('id');
        if( !$productId ) {
            $this->getResponse()->setRedirect(Mage::getBaseUrl());
        }

        $this->loadLayout(array('default', 'productReviews'), 'reviews');

        Mage::register('productId', $productId);

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('review/list_detailed')
                ->setUseBackLink(true)
                ->setUsePager(true)
        );

        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout(array('default', 'reviews'), 'reviews');
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('review/view')
        );

        $this->renderLayout();
    }
}