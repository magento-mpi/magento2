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
 * @package    Mage_Review
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review controller
 *
 * @category   Mage
 * @package    Mage_Review
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

                Mage::getSingleton('review/session')
                    ->addSuccess('Your review has been accepted for moderation');
            }
            catch (Exception $e){
                Mage::getSingleton('review/session')
                    ->addSuccess('Unable to post review. Please, try again later.');
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
        $this->_initLayoutMessages('review/session');


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
        $this->_initLayoutMessages('review/session');
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('review/view')
        );

        $this->renderLayout();
    }
}