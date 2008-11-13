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
 * @category   Mage
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * GoogleBase Admin Items Controller
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @name       Mage_GoogleBase_ItemTypesController
 * @author     Magento Core Team <core@magentocommerce.com>
*/
class Mage_GoogleBase_ItemsController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/googlebase')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog'), Mage::helper('adminhtml')->__('Catalog'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Google Base'), Mage::helper('adminhtml')->__('Google Base'));
        return $this;
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items'), Mage::helper('adminhtml')->__('Items'))
            ->_setActiveMenu('googlebase/items')
            ->_addContent($this->getLayout()->createBlock('googlebase/adminhtml_items'))
            ->renderLayout();
        if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
            $switchBlock->setDefaultStoreName($this->__('Default Values'))
                ->setWebsiteIds($product->getWebsiteIds())
                ->setSwitchUrl($this->getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'store'=>null)));
        }
    }

    public function gridAction()
    {
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('googlebase/adminhtml_items_item')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
           );
    }

    public function massAddAction ()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $productIds = $this->getRequest()->getParam('product');

        $totalAdded = 0;

        try {
            foreach ($productIds as $productId) {
                $product = Mage::getSingleton('catalog/product')
                    ->setStoreId($storeId)
                    ->load($productId);

                if ($product->getId()) {
                    Mage::getModel('googlebase/item')
                        ->setProduct($product)
                        ->insertItem()
                        ->save();

                    $totalAdded++;
                }
            }
            if ($totalAdded > 0) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d product(s) were successfully added to Google Base', $totalAdded)
                );
            } else {
                $this->_getSession()->addError($this->__('No products were added to Google Base'));
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index', array('store'=>$storeId));
    }

    public function massDeleteAction()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $itemIds = $this->getRequest()->getParam('item');

        $totalDeleted = 0;

        try {
            foreach ($itemIds as $itemId) {
                $item = Mage::getModel('googlebase/item')->load($itemId);
                if ($item->getId()) {
                    $item->deleteItem();
                    $item->delete();
                    $totalDeleted++;
                }
            }
            if ($totalDeleted > 0) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d items(s) were successfully removed from Google Base', $totalDeleted)
                );
            } else {
                $this->_getSession()->addError($this->__('No items were deleted from Google Base'));
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index', array('store'=>$storeId));
    }

    public function massPublishAction ()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $itemIds = $this->getRequest()->getParam('item');

        $totalPublished = 0;

        try {
            foreach ($itemIds as $itemId) {
                $item = Mage::getModel('googlebase/item')->load($itemId);
                if ($item->getId()) {
                    $item->activateItem();
                    $totalPublished++;
                }
            }
            if ($totalPublished > 0) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d items(s) were successfully published', $totalPublished)
                );
            } else {
                $this->_getSession()->addError($this->__('No items were published'));
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index', array('store'=>$storeId));
    }

    public function massHideAction ()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $itemIds = $this->getRequest()->getParam('item');

        $totalHidden = 0;

        try {
            foreach ($itemIds as $itemId) {
                $item = Mage::getModel('googlebase/item')->load($itemId);
                if ($item->getId()) {
                    $item->hideItem();
                    $totalHidden++;
                }
            }
            if ($totalHidden > 0) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d items(s) were successfully saved as Inactive items', $totalHidden)
                );
            } else {
                $this->_getSession()->addError($this->__('No items were saved as Inactive items'));
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index', array('store'=>$storeId));
    }

    /**
     *  Description goes here...
     *
     *  @param    none
     *  @return	  void
     */
    public function refreshAction()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $totalUpdated = 0;
        $totalDeleted = 0;

        try {
            $existing = array();
            $collection = Mage::getResourceModel('googlebase/item_collection')->load();
            foreach ($collection as $item) {
                $existing[$item->getGbaseItemId()] = array(
                    'id'    => $item->getId(),
                    'is_hidden' => $item->getIsHidden(),
                );
            }

            $stats = Mage::getModel('googlebase/service_feed')->getItemsStatsArray();
            foreach ($existing as $entryId => $data) {
                if (!isset($stats[$entryId])) {
                    Mage::getModel('googlebase/item')->load($existing[$entryId]['id'])->delete();
                    $totalDeleted++;
                }
                if (isset($stats[$entryId]) && $stats[$entryId]['draft'] != $existing[$entryId]['is_hidden']) {
                    Mage::getModel('googlebase/item')
                        ->load($existing[$entryId]['id'])
                        ->setIsHidden($stats[$entryId]['draft'])
                        ->save();
                    $totalUpdated++;
                }
            }
            $this->_getSession()->addSuccess(
                $this->__('Total of %d items(s) were successfully deleted, Total of %d items(s) were successfully updated', $totalDeleted, $totalUpdated)
            );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index', array('store'=>$storeId));

    }
}