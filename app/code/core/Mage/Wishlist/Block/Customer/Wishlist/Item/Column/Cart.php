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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist block customer item cart column
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Cart extends Mage_Wishlist_Block_Customer_Wishlist_Item_Column
{
    /*
     * List of product type configuration to render options list
     *
     * @var array
     */
    protected $_optionsCfg = array();

    /**
     * Init options renderer
     */
    public function _construct()
    {
        parent::_construct();
        $this->addOptionsRenderCfg('default', 'catalog/product_configuration', 'wishlist/options_list.phtml');
    }

    /**
     * Returns qty to show visually to user
     *
     * @param Mage_Wishlist_Model_Item $item
     * @return float
     */
    public function getAddToCartQty(Mage_Wishlist_Model_Item $item)
    {
        $qty = $item->getQty();
        return $qty ? $qty : 1;
    }

    /**
     * Retrieve child blocks html
     *
     * @param string $name
     * @param Mage_Core_Block_Abstract $child
     * @return string
     */
    protected function _beforeChildToHtml($name, $child)
    {
        $child->setItem($this->getItem());
    }

    /**
     * Retrieve column related javascript code
     *
     * @return string
     */
    public function getJs()
    {
        $js = "
            function addWItemToCart(itemId) {
                var url = '" . $this->getItemAddToCartUrl('%item%') . "';
                url = url.gsub('%item%', itemId);
                var form = $('wishlist-view-form');
                if (form) {
                    var input = form['qty[' + itemId + ']'];
                    if (input) {
                        var separator = (url.indexOf('?') >= 0) ? '&' : '?';
                        url += separator + input.name + '=' + encodeURIComponent(input.value);
                    }
                }
                setLocation(url);
            }
        ";

        foreach ($this->getSortedChildBlocks() as $child) {
            $js .= $child->getJs();
        }
        return $js;
    }

    /*
    * Adds config for rendering product type options
    * If template is null - later default will be used
    *
    * @param string $productType
    * @param string $helperName
    * @param null|string $template
    * @return Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Cart
    */
    public function addOptionsRenderCfg($productType, $helperName, $template = null)
    {
        $this->_optionsCfg[$productType] = array('helper' => $helperName, 'template' => $template);
        return $this;
    }

    /**
     * Get item options renderer config
     *
     * @param string $productType
     * @return array|null
     */
    public function getOptionsRenderCfg($productType)
    {
        if (isset($this->_optionsCfg[$productType])) {
            return $this->_optionsCfg[$productType];
        } elseif (isset($this->_optionsCfg['default'])) {
            return $this->_optionsCfg['default'];
        } else {
            return null;
        }
    }

    /**
     * Returns html for showing item options
     *
     * @param Mage_Wishlist_Model_Item $item
     * @return string
     */
    public function getDetailsHtml(Mage_Wishlist_Model_Item $item)
    {
        $cfg = $this->getOptionsRenderCfg($item->getProduct()->getTypeId());
        if (!$cfg) {
            return '';
        }

        $helper = Mage::helper($cfg['helper']);
        if (!($helper instanceof Mage_Catalog_Helper_Product_Configuration_Interface)) {
            Mage::throwException($this->__("Helper for wishlist options rendering doesn't implement required interface."));
        }

        $block = $this->getChild('item_options');
        if (!$block) {
            return '';
        }

        if ($cfg['template']) {
            $template = $cfg['template'];
        } else {
            $cfgDefault = $this->getOptionsRenderCfg('default');
            if (!$cfgDefault) {
                return '';
            }
            $template = $cfgDefault['template'];
        }

        return $block->setTemplate($template)
            ->setOptionList($helper->getOptions($item))
            ->toHtml();
    }
}
