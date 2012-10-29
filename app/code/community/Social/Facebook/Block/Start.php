<?php
/**
 * {license_notice}
 *
 * @category    Social
 * @package     Social_Facebook
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Social_Facebook_Block_Start extends Mage_Core_Block_Template
{
    const FACEBOOK_BLOCK_NO_TEXT        = 0;
    const FACEBOOK_BLOCK_START_CONNECT  = 1;
    const FACEBOOK_BLOCK_START_FRIENDS  = 2;

    protected $_template = 'empty.phtml';

    /**
     * Block Initialization
     *
     * @return
     */
    protected function _construct()
    {
        if (!Mage::helper('Social_Facebook_Helper_Data')->isEnabled()) {
            return;
        }
        parent::_construct();



        $this->setShowSumm(Social_Facebook_Block_Start::FACEBOOK_BLOCK_NO_TEXT);

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('product');
        $session = Mage::getSingleton('Mage_Core_Model_Session');
        $session->setData('product_id', $product->getId());
        $session->setData('product_url', $product->getUrlModel()->getUrlInStore($product));

        $accessToken = $session->getData('access_token');
        $facebookId  = $session->getData('facebook_id');

        $this->setPeopleCount(
            Mage::getModel('Social_Facebook_Model_Facebook')->getCountByProduct($product->getId())
        );

        if (!$accessToken) {
            $this->setShowSumm(Social_Facebook_Block_Start::FACEBOOK_BLOCK_START_CONNECT);
            $this->setConnectUrl(Mage::helper('Social_Facebook_Helper_Data')->getRedirectUrl($product));
            $session->unsetData('facebook_action');
            $session->setData('no_boxes', 1);
        } else {
            $actions = Mage::helper('Social_Facebook_Helper_Data')->getAllActions();
            $users  = array();
            foreach ($actions as $action) {
                $data = Mage::getModel('Social_Facebook_Model_Facebook')->getLinkedFriends($facebookId, $product->getId(),
                    $action['action']);
                if (!empty($data)) {
                    break;
                }
            }

            if (empty($data)) {
                $this->setShowSumm(Social_Facebook_Block_Start::FACEBOOK_BLOCK_START_FRIENDS);
                $session->setData('no_boxes', 1);
            } else {
                $session->unsetData('no_boxes');
            }
        }
    }
}
