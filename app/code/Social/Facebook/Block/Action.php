<?php
/**
 * {license_notice}
 *
 * @category    Social
 * @package     Social_Facebook
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Social_Facebook_Block_Action extends Mage_Core_Block_Template
{
    /**
     * Block Initialization
     *
     * @return Social_Facebook_Block_Action
     */
    protected function _construct()
    {
        if (!Mage::helper('Social_Facebook_Helper_Data')->isEnabled()) {
            return;
        }
        parent::_construct();

        $product = Mage::registry('product');
        $this->setProductId($product->getId());

        $this->setAllActions(Mage::helper('Social_Facebook_Helper_Data')->getAllActions());

        return $this;
    }

    /**
     * Get Url for redirect to Facebook
     *
     * @param string $action
     * @return string
     */
    public function getFacebookUrl($action)
    {
        return $this->getUrl(
            'facebook/index/redirect/',
            array(
                'productId' => $this->getProductId(),
                'action'    => $this->escapeHtml($action),
            ));
    }
}
