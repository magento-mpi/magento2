<?php
/**
 * Reviews admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Catalog_Product_ReviewController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog/review');

        $this->_addContent($this->getLayout()->createBlock('adminhtml/review_main'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog/review');

        $this->_addContent($this->getLayout()->createBlock('adminhtml/review_edit'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        $url = $this->getRequest()->getServer('HTTP_REFERER', Mage::getBaseUrl());

        $reviewId = $this->getRequest()->getParam('id', false);
        if ($data = $this->getRequest()->getPost()) {
            $review = Mage::getModel('review/review')->setData($data);
            try {
                $review->setId($reviewId)
                    ->save();

                $arrRatingId = $this->getRequest()->getParam('ratings', array());
                foreach ($arrRatingId as $voteId=>$optionId) {
                	Mage::getModel('rating/rating')
                	   ->setVoteId($voteId)
                	   ->setReviewId($review->getId())
                	   ->updateOptionVote($optionId);
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(__('Review successfully saved.'));
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
                return;
            } catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect($url);
    }

    public function deleteAction()
    {
        $url = $this->getRequest()->getServer('HTTP_REFERER', Mage::getBaseUrl());

        $reviewId = $this->getRequest()->getParam('id', false);

        try {
            Mage::getModel('review/review')->setId($reviewId)
                ->delete();

            Mage::getSingleton('adminhtml/session')->addSuccess(__('Review successfully deleted.'));
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
            return;
        } catch (Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->getResponse()->setRedirect($url);
    }
}