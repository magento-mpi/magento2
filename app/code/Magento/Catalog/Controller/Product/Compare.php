<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog comapare controller
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Controller_Product_Compare extends Magento_Core_Controller_Front_Action
{
    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('add');

    /**
     * Customer id
     *
     * @var null|int
     */
    protected $_customerId = null;

    /**
     * Compare index action
     */
    public function indexAction()
    {
        $items = $this->getRequest()->getParam('items');

        $beforeUrl = $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED);
        if ($beforeUrl) {
            Mage::getSingleton('Magento_Catalog_Model_Session')
                ->setBeforeCompareUrl(Mage::helper('Magento_Core_Helper_Data')->urlDecode($beforeUrl));
        }

        if ($items) {
            $items = explode(',', $items);
            $list = Mage::getSingleton('Magento_Catalog_Model_Product_Compare_List');
            $list->addProducts($items);
            $this->_redirect('*/*/*');
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Add item to compare list
     */
    public function addAction()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId
            && (Mage::getSingleton('Magento_Log_Model_Visitor')->getId()
                || Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn())
        ) {
            $product = Mage::getModel('Magento_Catalog_Model_Product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if ($product->getId()/* && !$product->isSuper()*/) {
                Mage::getSingleton('Magento_Catalog_Model_Product_Compare_List')->addProduct($product);
                $productName = Mage::helper('Magento_Core_Helper_Data')->escapeHtml($product->getName());
                Mage::getSingleton('Magento_Catalog_Model_Session')->addSuccess(
                    __('You added product %1 to the comparison list.', $productName)
                );
                $this->_eventManager->dispatch('catalog_product_compare_add_product', array('product'=>$product));
            }

            Mage::helper('Magento_Catalog_Helper_Product_Compare')->calculate();
        }

        $this->_redirectReferer();
    }

    /**
     * Remove item from compare list
     */
    public function removeAction()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('Magento_Catalog_Model_Product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if ($product->getId()) {
                /** @var $item Magento_Catalog_Model_Product_Compare_Item */
                $item = Mage::getModel('Magento_Catalog_Model_Product_Compare_Item');
                if (Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()) {
                    $item->addCustomerData(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer());
                } elseif ($this->_customerId) {
                    $item->addCustomerData(
                        Mage::getModel('Magento_Customer_Model_Customer')->load($this->_customerId)
                    );
                } else {
                    $item->addVisitorId(Mage::getSingleton('Magento_Log_Model_Visitor')->getId());
                }

                $item->loadByProduct($product);
                /** @var $helper Magento_Catalog_Helper_Product_Compare */
                $helper = Mage::helper('Magento_Catalog_Helper_Product_Compare');
                if ($item->getId()) {
                    $item->delete();
                    $productName = $helper->escapeHtml($product->getName());
                    Mage::getSingleton('Magento_Catalog_Model_Session')->addSuccess(
                        __('You removed product %1 from the comparison list.', $productName)
                    );
                    $this->_eventManager->dispatch('catalog_product_compare_remove_product', array('product' => $item));
                    $helper->calculate();
                }
            }
        }

        if (!$this->getRequest()->getParam('isAjax', false)) {
            $this->_redirectReferer();
        }
    }

    /**
     * Remove all items from comparison list
     */
    public function clearAction()
    {
        $items = Mage::getResourceModel('Magento_Catalog_Model_Resource_Product_Compare_Item_Collection');

        if (Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId(Mage::getSingleton('Magento_Log_Model_Visitor')->getId());
        }

        /** @var $session Magento_Catalog_Model_Session */
        $session = Mage::getSingleton('Magento_Catalog_Model_Session');

        try {
            $items->clear();
            $session->addSuccess(__('You cleared the comparison list.'));
            Mage::helper('Magento_Catalog_Helper_Product_Compare')->calculate();
        } catch (Magento_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, __('Something went wrong  clearing the comparison list.'));
        }

        $this->_redirectReferer();
    }

    /**
     * Setter for customer id
     *
     * @param int $customerId
     * @return Magento_Catalog_Controller_Product_Compare
     */
    public function setCustomerId($customerId)
    {
        $this->_customerId = $customerId;
        return $this;
    }
}
