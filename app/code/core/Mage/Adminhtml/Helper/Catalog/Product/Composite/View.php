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
 * Adminhtml catalog product composite view helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Catalog_Product_Composite_View extends Mage_Core_Helper_Abstract
{
     /**
     * Init composite product configuration layout
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Adminhtml_Controller_Action $controller
     * @return Mage_Adminhtml_Helper_Catalog_Product_Composite_View
     */
    public function initProductLayout($product, $controller)
    {
        $controller->getLayout()->getUpdate()
            ->addHandle('ADMINHTML_CATALOG_PRODUCT_COMPOSITE_CONFIGURE')
            ->addHandle('PRODUCT_TYPE_' . $product->getTypeId());
        $controller->addActionLayoutHandles();
        $controller->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        return $this;
    }

    /**
     * Prepares and render composite product fieldset - inits layout and all needed stuff
     *
     * $params can have following values:
     *   - 'buy_request' - Varien_Object holding buyRequest to configure product
     *
     * @param int $productId
     * @param Mage_Adminhtml_Controller_Action $controller
     * @param null|Varien_Object $params
     * @return Mage_Adminhtml_Helper_Catalog_Product_Composite_View
     */
    public function prepareAndRender($productId, $controller, Varien_Object $params = null)
    {
        // Prepare data
        $productHelper = Mage::helper('catalog/product');
        if (!$params) {
            $params = new Varien_Object();
        }

        $currentStoreId = (int) $params->getCurrentStoreId();
        if (!$currentStoreId) {
            $currentStoreId = Mage::app()->getStore()->getId();
        }

        $product = Mage::getModel('catalog/product')
            ->setStoreId($currentStoreId)
            ->load($productId);
        if (!$product) {
            new Mage_Core_Exception($this->__('Product is not loaded'));
        }
        Mage::register('current_product', $product);
        Mage::register('product', $product);

        $currentCustomerId = (int) $params->getCurrentCustomerId();
        if ($currentCustomerId) {
            $currentCustomer = Mage::getModel('customer/customer')->load($currentCustomerId);
            Mage::register('current_customer', $currentCustomer);
        }

        $buyRequest = $params->getBuyRequest();
        if ($buyRequest) {
            $productHelper->prepareProductOptions($product, $buyRequest);
        }

        $this->initProductLayout($product, $controller);
        $controller->renderLayout();
    }
}
