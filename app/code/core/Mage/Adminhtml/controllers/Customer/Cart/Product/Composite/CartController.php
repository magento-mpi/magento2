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

class Mage_Adminhtml_Customer_Cart_Product_Composite_CartController extends Mage_Adminhtml_Controller_Action
{
    /*
     * Ajax handler to response configuration fieldset of composite product in customer's cart
     *
     * @return void
     */
    public function configureAction()
    {
        $quoteItemId = (int) $this->getRequest()->getParam('id');
        $customerId = (int) $this->getRequest()->getParam('customer_id');
        $websiteId = (int) $this->getRequest()->getParam('website_id');

        $customer = Mage::getModel('customer/customer')
            ->load($customerId);

        /* @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getModel('sales/quote')
            ->setWebsite(Mage::app()->getWebsite($websiteId))
            ->loadByCustomer($customer);

        $quoteItem = $quote->getItemById($quoteItemId);

        if ($quoteItem) {
            $optionCollection = Mage::getModel('sales/quote_item_option')
                ->getCollection()
                ->addItemFilter(array($quoteItemId));
            $quoteItem->setOptions($optionCollection->getOptionsByItem($quoteItem));

            $viewHelper = Mage::helper('adminhtml/catalog_product_composite_view');
            $params = new Varien_Object();

            $params->setBuyRequest($quoteItem->getBuyRequest());
            $params->setCurrentStoreId($quoteItem->getStoreId());
            $params->setCurrentCustomer($customer);

            // Render page
            $viewHelper->prepareAndRender($quoteItem->getProductId(), $this, $params);
        }
    }

    /*
     * IFrame handler for submitted configuration for quote item
     *
     * @return void
     */
    public function updateAction()
    {
        // Update item configuration
        $quoteItemId = (int) $this->getRequest()->getParam('id');
        $customerId = (int) $this->getRequest()->getParam('customer_id');
        $websiteId = (int) $this->getRequest()->getParam('website_id');
        $errorMessage = null;
        try {
            if (!$customerId) {
                Mage::throwException($this->__('No customer id defined'));
            }

            $customer = Mage::getModel('customer/customer')
                ->load($customerId);

            /* @var $quote Mage_Sales_Model_Quote */
            $quote = Mage::getModel('sales/quote')
                ->setWebsite(Mage::app()->getWebsite($websiteId))
                ->loadByCustomer($customer);

            $buyRequest = new Varien_Object($this->getRequest()->getParams());
            $quote->updateItem($quoteItemId, $buyRequest);
            $quote->collectTotals()
                ->save();
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }

        // Form result for client javascript
        $updateResult = new Varien_Object();
        if ($errorMessage) {
            $updateResult->setError(1);
            $updateResult->setMessage($errorMessage);
        } else {
            $updateResult->setOk(1);
        }

        /* @var $helper Mage_Adminhtml_Helper_Catalog_Product_Composite */
        $helper = Mage::helper('adminhtml/catalog_product_composite');
        $helper->renderUpdateResult($this, $updateResult);
    }
}
