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
class Mage_Review_ReviewController extends Mage_Core_Controller_Front_Action
{
    public function productPostAction()
    {
        $url = $this->getRequest()->getServer('HTTP_REFERER', Mage::getBaseUrl());
        
        if ($data = $this->getRequest()->getPost()) {
            $review = Mage::getModel('review/review')->setData($data);
            try {
                $review->setEntityId(1) // product
                    ->setEntityPkValue($this->getRequest()->getParam('id', false))
                    ->setStatusId(1) // approved
                    ->setWebsiteId(Mage::getSingleton('core/website')->getId())
                    ->save();
                    
                $arrRatingId = $this->getRequest()->getParam('ratings', array());
                foreach ($arrRatingId as $ratingId=>$optionId) {
                	Mage::getModel('rating/rating')
                	   ->setRatingId($ratingId)
                	   ->addOptionVote($optionId, $review->getId());
                }
                
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
}
