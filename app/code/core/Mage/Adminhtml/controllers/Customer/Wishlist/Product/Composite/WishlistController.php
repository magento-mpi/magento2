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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog composite product configuration controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Customer_Wishlist_Product_Composite_WishlistController extends Mage_Adminhtml_Controller_Action
{
    /*
     * Ajax handler to response configuration fieldset of composite product in customer's cart
     */
    public function configureAction()
    {
        $wishlistItemId = (int) $this->getRequest()->getParam('id');
        if ($wishlistItemId) {
            $wishlistItem = Mage::getModel('wishlist/item')->load($wishlistItemId);
            if ($wishlistItem) {
                $viewHelper = Mage::helper('adminhtml/catalog_product_composite_view');
                $params = new Varien_Object();

                $params->setBuyRequest($wishlistItem->getBuyRequest());
                $params->setCurrentStoreId($wishlistItem->getStoreId());

                // Render page
                $viewHelper->prepareAndRender($wishlistItem->getProductId(), $this, $params);
            }
        }
    }
}
