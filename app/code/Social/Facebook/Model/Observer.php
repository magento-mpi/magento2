<?php
/**
 * {license_notice}
 *
 * @category    Social
 * @package     Social_Facebook
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Facebook Observer
 *
 * @category   Social
 * @package    Social_Facebook
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Social_Facebook_Model_Observer
{
    /**
     * Save & Send Actions to Facebook
     *
     * @return Social_Facebook_Model_Observer
     */
    public function catalogProduct()
    {
        if (!Mage::helper('Social_Facebook_Helper_Data')->isEnabled()) {
            return false;
        }
        $session        = Mage::getSingleton('Mage_Core_Model_Session');

        $facebookAction = $session->getData('facebook_action');
        $productId      = $session->getData('product_id');
        $productUrl     = $session->getData('product_url');

        /** @var $facebookModel Social_Facebook_Model_Facebook */
        $facebookModel  = Mage::getSingleton('Social_Facebook_Model_Facebook');

        if ($facebookAction) {
            $result = $facebookModel->sendFacebookAction();

            if (!empty($result)) {
                $session->addSuccess(Mage::helper('Social_Facebook_Helper_Data')->__('I %1 this product', $facebookAction));
                $session->unsetData('facebook_action');

                $user = $facebookModel->getFacebookUser();
                if ($user) {
                    $facebookUser = $facebookModel->loadUserByActionId($facebookAction, $user['facebook_id'],
                        $productId);

                    if (!$facebookUser) {
                        $data = array(
                            'facebook_id'       => $user['facebook_id'],
                            'facebook_action'   => $facebookAction,
                            'facebook_name'     => $user['facebook_name'],
                            'item_id'           => $productId
                        );

                        $facebookModel->setData($data)
                            ->save();
                    }
                }
                Mage::app()->getResponse()->setRedirect($productUrl);
                Mage::app()->getResponse()->sendResponse();
                exit();
            }
        }

        if (!isset($facebookId)) {
            $user = $facebookModel->getFacebookUser();
            if ($user) {
                $facebookId = $user['facebook_id'];
            }

        }
        if (isset($facebookId)) {
            $this->_cacheFriends($facebookId);
        }

        return $this;
    }

    /**
     * Cache Facebook Friends
     *
     * @param int $facebookId
     * @return Social_Facebook_Model_Observer
     */
    protected function _cacheFriends($facebookId)
    {
        $facebookModel = Mage::getSingleton('Social_Facebook_Model_Facebook');

        $users  = $facebookModel->cacheFriends(array(), $facebookId);

        if (empty($users)) {
            $result = $facebookModel->getFacebookFriends();
            if (!empty($result)) {
                $facebookModel->cacheFriends($result, $facebookId);
            }
        }

        return $this;
    }
}
