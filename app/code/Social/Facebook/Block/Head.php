<?php
/**
 * {license_notice}
 *
 * @category    Social
 * @package     Social_Facebook
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Social_Facebook_Block_Head extends Mage_Core_Block_Template
{
    /**
     * Block Initialization
     *
     * @return Social_Facebook_Block_Head
     */
    protected function _construct()
    {
        $helper = Mage::helper('Social_Facebook_Helper_Data');
        if (!$helper->isEnabled()) {
            return;
        }
        parent::_construct();

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('product');

        if ($product) {
            $tags[] = array(
                'property'  => 'fb:app_id',
                'content'   => $helper->getAppId()
            );
            $tags[] = array(
                'property'  => 'og:type',
                'content'   => $helper->getAppName() . ':' . $helper->getObjectType()
            );
            $tags[] = array(
                'property'  => 'og:url',
                'content'   => Mage::getUrl('facebook/index/page', array('id' => $product->getId()))
            );
            $tags[] = array(
                'property'  => 'og:title',
                'content'   => $this->escapeHtml($product->getName())
            );
            $tags[] = array(
                'property'  => 'og:image',
                'content'   => $this->escapeHtml(Mage::helper('Mage_Catalog_Helper_Image')->init($product, 'image')->resize(
                    $this->getVar('product_base_image_size', 'Mage_Catalog')
                ))
            );
            $tags[] = array(
                'property'  => 'og:description',
                'content'   => $this->escapeHtml($product->getShortDescription())
            );
            $tags[] = array(
                'property'  => $helper->getAppName(). ':price',
                'content'   => Mage::helper('Mage_Core_Helper_Data')->currency($product->getFinalPrice(), true, false)
            );

            $this->setMetaTags($tags);

            $this->setRedirectUrl($product->getUrlModel()->getUrlInStore($product));

            $this->setAppName($helper->getAppName());
        }

        return $this;
    }
}