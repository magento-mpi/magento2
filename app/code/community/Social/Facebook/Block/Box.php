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
 * @category    Social
 * @package     Social_Facebook
 * @copyright   Copyright (c) 2009 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
