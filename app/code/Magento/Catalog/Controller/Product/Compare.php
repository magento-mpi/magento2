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
namespace Magento\Catalog\Controller\Product;

class Compare extends \Magento\Core\Controller\Front\Action
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
            \Mage::getSingleton('Magento\Catalog\Model\Session')
                ->setBeforeCompareUrl(\Mage::helper('Magento\Core\Helper\Data')->urlDecode($beforeUrl));
        }

        if ($items) {
            $items = explode(',', $items);
            $list = \Mage::getSingleton('Magento\Catalog\Model\Product\Compare\ListCompare');
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
            && (\Mage::getSingleton('Magento\Log\Model\Visitor')->getId()
                || \Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn())
        ) {
            $product = \Mage::getModel('\Magento\Catalog\Model\Product')
                ->setStoreId(\Mage::app()->getStore()->getId())
                ->load($productId);

            if ($product->getId()/* && !$product->isSuper()*/) {
                \Mage::getSingleton('Magento\Catalog\Model\Product\Compare\ListCompare')->addProduct($product);
                $productName = \Mage::helper('Magento\Core\Helper\Data')->escapeHtml($product->getName());
                \Mage::getSingleton('Magento\Catalog\Model\Session')->addSuccess(
                    __('You added product %1 to the comparison list.', $productName)
                );
                $this->_eventManager->dispatch('catalog_product_compare_add_product', array('product'=>$product));
            }

            \Mage::helper('Magento\Catalog\Helper\Product\Compare')->calculate();
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
            $product = \Mage::getModel('\Magento\Catalog\Model\Product')
                ->setStoreId(\Mage::app()->getStore()->getId())
                ->load($productId);

            if ($product->getId()) {
                /** @var $item \Magento\Catalog\Model\Product\Compare\Item */
                $item = \Mage::getModel('\Magento\Catalog\Model\Product\Compare\Item');
                if (\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
                    $item->addCustomerData(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer());
                } elseif ($this->_customerId) {
                    $item->addCustomerData(
                        \Mage::getModel('\Magento\Customer\Model\Customer')->load($this->_customerId)
                    );
                } else {
                    $item->addVisitorId(\Mage::getSingleton('Magento\Log\Model\Visitor')->getId());
                }

                $item->loadByProduct($product);
                /** @var $helper \Magento\Catalog\Helper\Product\Compare */
                $helper = \Mage::helper('Magento\Catalog\Helper\Product\Compare');
                if ($item->getId()) {
                    $item->delete();
                    $productName = $helper->escapeHtml($product->getName());
                    \Mage::getSingleton('Magento\Catalog\Model\Session')->addSuccess(
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
        $items = \Mage::getResourceModel('\Magento\Catalog\Model\Resource\Product\Compare\Item\Collection');

        if (\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            $items->setCustomerId(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId(\Mage::getSingleton('Magento\Log\Model\Visitor')->getId());
        }

        /** @var $session \Magento\Catalog\Model\Session */
        $session = \Mage::getSingleton('Magento\Catalog\Model\Session');

        try {
            $items->clear();
            $session->addSuccess(__('You cleared the comparison list.'));
            \Mage::helper('Magento\Catalog\Helper\Product\Compare')->calculate();
        } catch (\Magento\Core\Exception $e) {
            $session->addError($e->getMessage());
        } catch (\Exception $e) {
            $session->addException($e, __('Something went wrong  clearing the comparison list.'));
        }

        $this->_redirectReferer();
    }

    /**
     * Setter for customer id
     *
     * @param int $customerId
     * @return \Magento\Catalog\Controller\Product\Compare
     */
    public function setCustomerId($customerId)
    {
        $this->_customerId = $customerId;
        return $this;
    }
}
