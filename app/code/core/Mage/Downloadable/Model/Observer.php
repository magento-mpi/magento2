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
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable Products Observer
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Observer
{
    /**
     * Prepare product to save
     *
     * @param   Varien_Object $observer
     * @return  Mage_Downloadable_Model_Observer
     */
    public function prepareProductSave($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();

        if ($downloadable = $request->getPost('downloadable')) {
            $product->setDownloadableData($downloadable);
        }

        return $this;
    }

    /**
     * Save data from order to purchased links
     *
     * @param Varien_Object $observer
     * @return Mage_Downloadable_Model_Observer
     */
    public function saveDownloadableOrderItem($observer)
    {
        $orderItem = $observer->getEvent()->getItem();
        $product = Mage::getModel('catalog/product')
            ->load($orderItem->getProductId());
        if ($product->getTypeId() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
            $links = $product->getTypeInstance()->getLinks();
            if ($linkIds = $orderItem->getProductOptionByCode('links')) {
                foreach ($linkIds as $linkId) {
                    if (isset($links[$linkId])) {
                        $linkPurchased = Mage::getModel('downloadable/link_purchased');
                        Mage::helper('core')->copyFieldset(
                            'downloadable_sales_copy_order_item',
                            'to_downloadable',
                            $orderItem,
                            $linkPurchased
                        );
                        Mage::helper('core')->copyFieldset(
                            'downloadable_sales_copy_order',
                            'to_downloadable',
                            $orderItem->getOrder(),
                            $linkPurchased
                        );
                        Mage::helper('core')->copyFieldset(
                            'downloadable_sales_copy_link',
                            'to_purchased',
                            $links[$linkId],
                            $linkPurchased
                        );
                        $numberOfDownloads = $links[$linkId]->getNumberOfDownloads()*$orderItem->getQtyOrdered();
                        $linkPurchased->setNumberOfDownloadsBought($numberOfDownloads)
                            ->setStatus(Mage_Downloadable_Model_Link_Purchased::LINK_STATUS_PENDING)
                            ->save();
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Set checkout session flag if order has downloadable product(s)
     *
     * @param Varien_Object $observer
     * @return Mage_Downloadable_Model_Observer
     */
    public function setHasDownloadableProducts($observer)
    {
        $session = Mage::getSingleton('checkout/session');
        if (!$session->getHasDownloadableProducts()) {
            $order = $observer->getEvent()->getOrder();
            foreach ($order->getAllVisibleItems() as $item) {
                if ($item->getProductType() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
                    $session->setHasDownloadableProducts(true);
                    break;
                }
            }
        }
        return $this;
    }

}
