<?php
/**
 * {license_notice}
 *
 * @category    Social
 * @package     Social_Facebook
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Social_Facebook_Block_Box extends Mage_Core_Block_Template
{
    /**
     * Block Initialization
     *
     * @return Social_Facebook_Block_Box
     */
    protected function _construct()
    {
        $helper = Mage::helper('Social_Facebook_Helper_Data');
        if (!$helper->isEnabled() || Mage::getSingleton('Mage_Core_Model_Session')->getNoBoxes()) {
            return;
        }
        parent::_construct();

        $product = Mage::registry('product');
        $this->setProductId($product->getId());

        $this->setAllActions($helper->getAllActions());

        $this->setFacebookId(Mage::getSingleton('Mage_Core_Model_Session')->getData('facebook_id'));

        return $this;
    }

    /**
     * Get Facebook Friend Box By Action
     *
     * @param string $action
     * @return array
     */
    public function getFriendBox($action)
    {
        return Mage::getModel('Social_Facebook_Model_Facebook')->getLinkedFriends($this->getFacebookId(),
            $this->getProductId(), $action);
    }

    /**
     * Get Count of Facebook User
     *
     * @param string $action
     * @return int
     */
    public function getCountOfUsers($action)
    {
        return Mage::getModel('Social_Facebook_Model_Facebook')->getCountByActionProduct(
            $this->escapeHtml($action),
            $this->getProductId()
        );
    }
}
