<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Links block
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Links extends Mage_Page_Block_Template_Links_Block
{
    /**
     * Position in link list
     * @var int
     */
    protected $_position = 30;

    /**
     * Set link title, label and url
     */
    public function __construct()
    {
        parent::__construct();
        $this->initLinkProperties();
    }

    /**
     * Define label, title and url for wishlist link
     */
    public function initLinkProperties()
    {
        if ($this->helper('Mage_Wishlist_Helper_Data')->isAllow()) {
            $count = $this->getItemCount() ? $this->getItemCount() : $this->helper('Mage_Wishlist_Helper_Data')->getItemCount();
            if ($count > 1) {
                $text = $this->__('My Wishlist (%d items)', $count);
            } else if ($count == 1) {
                $text = $this->__('My Wishlist (%d item)', $count);
            } else {
                $text = $this->__('My Wishlist');
            }
            $this->_label = $text;
            $this->_title = $text;
            $this->_url = $this->getUrl('wishlist');
        }
    }
}
