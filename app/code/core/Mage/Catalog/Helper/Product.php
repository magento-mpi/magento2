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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog category helper
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Helper_Product extends Mage_Core_Helper_Url
{
    protected $_statuses;

    protected $_canEmailToFriend;

    protected $_maxRecipients;

    protected $_maxSendsToFriend;

    protected $_sendToFriendCheckType;

    /**
     * Retrieve product view page url
     *
     * @param   mixed $product
     * @return  string
     */
    public function getProductUrl($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $urlKey = $product->getUrlKey() ? $product->getUrlKey() : $product->getName();
            $params = array(
                's'         => $this->_prepareString($urlKey),
                'id'        => $product->getId(),
                'category'  => $product->getCategoryId()
            );
            return $this->_getUrl('catalog/product/view', $params);
        }
        if ((int) $product) {
            return $this->_getUrl('catalog/product/view', array('id'=>$product));
        }
        return false;
    }

    /**
     * Retrieve product price
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  float
     */
    public function getPrice($product)
    {
        return $product->getPrice();
    }

    /**
     * Retrieve product final price
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  float
     */
    public function getFinalPrice($product)
    {
        return $product->getFinalPrice();
    }

    /**
     * Retrieve base image url
     *
     * @return string
     */
    public function getImageUrl($product)
    {
        $url = false;
        if (!$product->getImage()) {
            $url = Mage::getDesign()->getSkinUrl('images/no_image.jpg');
        }
        elseif ($attribute = $product->getResource()->getAttribute('image')) {
            $url = $attribute->getFrontend()->getUrl($this);
        }
        return $url;
    }

    /**
     * Retrieve small image url
     *
     * @return unknown
     */
    public function getSmallImageUrl($product)
    {
        $url = false;
        if (!$product->getSmallImage()) {
            $url = Mage::getDesign()->getSkinUrl('images/no_image.jpg');
        }
        elseif ($attribute = $product->getResource()->getAttribute('small_image')) {
            $url = $attribute->getFrontend()->getUrl($this);
        }
        return $url;
    }

    /**
     * Retrieve thumbnail image url
     *
     * @return unknown
     */
    public function getThumbnailUrl($product)
    {
        return '';
    }

    public function getEmailToFriendUrl($product)
    {
        return $this->_getUrl('catalog/product/send', array('id'=>$product->getId()));
    }

    /**
     * Retrieve product price html block
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  string
     */
    public function getPriceHtml($product)
    {
        $html = '';
        if ($product->getPrice() == $product->getFinalPrice()) {
            $html = '<div class="price-box">
                <span class="regular-price" id="product-price-'.$product->getId().'">
                '.Mage::helper('core')->currency($product->getPrice()).'
                </span><br/>
                </div>';
        }
        else {
            $html.= '<div class="price-box">
                <span class="special-price">
                    <span class="label">'.$this->__('Special Price:').'</span>
                    <span class="price" id="product-price-'.$product->getId().'">
                    '.Mage::helper('core')->currency($product->getFinalPrice()).'
                    </span>
                </span><br/>
                <span class="old-price">
                    <span class="label">'.$this->__('Regular Price:').'</span>
                    <span class="price">
                    '.Mage::helper('core')->currency($product->getPrice()).'
                    </span>
                </span>
            </div>';
        }
        return $html;
    }

    public function getStatuses()
    {
        if(is_null($this->_statuses)) {
            $this->_statuses = Mage::getModel('catalog/product_status')->getResourceCollection()->load();
        }

        return $this->_statuses;
    }

    /**
     * Check if user is allowed to email product to a friend
     *
     * @return boolean
     */
    public function canEmailToFriend()
    {
        if (isset($this->_canEmailToFriend)) {
            return $this->_canEmailToFriend;
        }
        if (!Mage::getStoreConfig('sendfriend/email/allow_guest')
            && !Mage::getSingleton('customer/session')->isLoggedIn()) {
            return false;
        }
        return true;
    }

    /**
     * Get max allowed recipients for "Send to a Friend" function
     *
     * @return integer
     */
    public function getMaxRecipients()
    {
        if (isset($this->_maxRecipients)) {
            return $this->_maxRecipients;
        }

        return max(0, (int) Mage::getStoreConfig('sendfriend/email/max_recipients'));
    }

    /**
     * Get max allowed uses of "Send to Friend" function per hour
     *
     * @return integer
     */
    public function getMaxSendsToFriend()
    {
        if (isset($this->_maxSendsToFriend)) {
            return $this->_maxSendsToFriend;
        }

        return max(0, (int) Mage::getStoreConfig('sendfriend/email/max_per_hour'));
    }

    /**
     * Get check type for "Send to Friend" function
     *
     * @return integer
     */
    public function getSendToFriendCheckType()
    {
        if (isset($this->_sendToFriendCheckType)) {
            return $this->_sendToFriendCheckType;
        }

        return max(0, (int) Mage::getStoreConfig('sendfriend/email/check_by'));
    }

}