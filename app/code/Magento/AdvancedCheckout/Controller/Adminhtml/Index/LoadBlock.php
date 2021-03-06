<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml\Index;

use Magento\AdvancedCheckout\Exception as AdvancedCheckoutException;
use Magento\Framework\Model\Exception;

class LoadBlock extends \Magento\AdvancedCheckout\Controller\Adminhtml\Index
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context, $registry, $customerBuilder);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * Reload quote
     *
     * @return $this
     */
    protected function _reloadQuote()
    {
        $id = $this->getCartModel()->getQuote()->getId();
        $this->getCartModel()->getQuote()->load($id);
        return $this;
    }

    /**
     * Loading page block
     *
     * @return void
     */
    public function execute()
    {
        $criticalException = false;
        try {
            $this->_initData(false)->_processData();
        } catch (AdvancedCheckoutException $e) {
            $this->messageManager->addError($e->getMessage());
            $criticalException = true;
        } catch (Exception $e) {
            $this->_reloadQuote();
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_reloadQuote();
            $this->messageManager->addException($e, $e->getMessage());
        }

        $asJson = $this->getRequest()->getParam('json');
        $block = $this->getRequest()->getParam('block');

        $resultLayout = $this->resultLayoutFactory->create();
        if ($asJson) {
            $resultLayout->addHandle('checkout_index_manage_load_block_json');
        } else {
            $resultLayout->addHandle('checkout_index_manage_load_block_plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                if ($criticalException && $block != 'message') {
                    continue;
                }
                $resultLayout->addHandle('checkout_index_manage_load_block_' . $block);
            }
        }
        $result = $resultLayout->getLayout()->renderElement('content');
        if ($this->getRequest()->getParam('as_js_varname')) {
            $this->_objectManager->get('Magento\Backend\Model\Session')->setUpdateResult($result);
            $this->_redirect('checkout/*/showUpdateResult');
        } else {
            $this->getResponse()->setBody($result);
        }
    }

    /**
     * Processing request data
     *
     * @return $this
     */
    protected function _processData()
    {
        /**
         * Update quote items
         */
        if ($this->getRequest()->getPost('update_items')) {
            if ((int)$this->getRequest()->getPost('empty_customer_cart') == 1) {
                // Empty customer's shopping cart
                $this->_objectManager->get('Magento\Sales\Model\QuoteRepository')->save(
                    $this->getCartModel()->getQuote()->removeAllItems()->collectTotals()
                );
            } else {
                $items = $this->getRequest()->getPost('item', []);
                $items = $this->_processFiles($items);
                $this->getCartModel()->updateQuoteItems($items);
                if ($this->getCartModel()->getQuote()->getHasError()) {
                    foreach ($this->getCartModel()->getQuote()->getErrors() as $error) {
                        /* @var $error \Magento\Framework\Message\Error */
                        $this->messageManager->addError($error->getText());
                    }
                }
            }
        }

        if ($this->getRequest()->getPost('sku_remove_failed')) {
            // "Remove all" button on error grid has been pressed: remove items from "add-by-SKU" queue
            $this->getCartModel()->removeAllAffectedItems();
        }

        $sku = $this->getRequest()->getPost('remove_sku', false);
        if ($sku) {
            $this->getCartModel()->removeAffectedItem($sku);
        }

        /**
         * Add products from different lists
         */
        $listTypes = $this->getRequest()->getPost('configure_complex_list_types');
        if ($listTypes) {
            $skuListTypes = [
                \Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\AbstractErrors::LIST_TYPE,
                \Magento\AdvancedCheckout\Block\Adminhtml\Sku\AbstractSku::LIST_TYPE,
            ];
            /* @var $productHelper \Magento\Catalog\Helper\Product */
            $productHelper = $this->_objectManager->get('Magento\Catalog\Helper\Product');
            $listTypes = array_filter(explode(',', $listTypes));
            if (in_array(\Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\AbstractErrors::LIST_TYPE, $listTypes)) {
                // If results came from SKU error grid - clean them (submitted results are going to be re-checked)
                $this->getCartModel()->removeAllAffectedItems();
            }
            $listItems = $this->getRequest()->getPost('list');
            foreach ($listTypes as $listType) {
                if (!isset(
                    $listItems[$listType]
                    ) || !is_array(
                        $listItems[$listType]
                    ) || !isset(
                    $listItems[$listType]['item']
                    ) || !is_array(
                        $listItems[$listType]['item']
                    )
                ) {
                    continue;
                }

                $items = $listItems[$listType]['item'];

                foreach ($items as $itemId => $info) {
                    if (!is_array($info)) {
                        // For sure to filter incoming data
                        $info = [];
                    }

                    $itemInfo = $this->_getInfoForListItem($listType, $itemId, $info);
                    if (!$itemInfo) {
                        continue;
                    }

                    $currentConfig = $itemInfo->getBuyRequest();
                    if (isset($info['_config_absent'])) {
                        // User has added items without configuration (using multiple checkbox control)
                        // Try to use configs from list
                        if (isset($info['qty'])) {
                            $currentConfig->setQty($info['qty']);
                        }
                        $config = $currentConfig->getData();
                    } else {
                        $params = [
                            'files_prefix' => 'list_' . $listType . '_item_' . $itemId . '_',
                            'current_config' => $currentConfig,
                        ];
                        $config = $productHelper->addParamsToBuyRequest($info, $params)->toArray();
                    }
                    if (in_array($listType, $skuListTypes)) {
                        // Items will be later added to cart using saveAffectedItems()
                        $this->getCartModel()->setAffectedItemConfig($itemId, $config);
                    } else {
                        try {
                            $this->getCartModel()->addProduct($itemInfo->getProductId(), $config);
                        } catch (Exception $e) {
                            $this->messageManager->addError($e->getMessage());
                        } catch (\Exception $e) {
                            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                        }
                    }
                }
            }
        }

        if (is_array($listTypes) && array_intersect($listTypes, $skuListTypes)) {
            $cart = $this->getCartModel();
            // We need to save products to magento_advancedcheckout/cart instead of checkout/cart
            $cart->saveAffectedProducts($cart, false);
        }

        /**
         * Remove quote item
         */
        $removeItemId = (int)$this->getRequest()->getPost('remove_item');
        $removeFrom = (string)$this->getRequest()->getPost('from');
        if ($removeItemId && $removeFrom) {
            $this->getCartModel()->removeItem($removeItemId, $removeFrom);
        }

        /**
         * Move quote item
         */
        $moveItemId = (int)$this->getRequest()->getPost('move_item');
        $moveTo = (string)$this->getRequest()->getPost('to');
        if ($moveItemId && $moveTo) {
            $this->getCartModel()->moveQuoteItem($moveItemId, $moveTo);
        }

        $this->getCartModel()->saveQuote();

        return $this;
    }

    /**
     * Wrapper for _getListItemInfo() - extends with additional list types. New method has been created to leave
     * definition of original method unchanged (add_by_sku list type utilizes additional parameter - $info).
     *
     * @param string $listType
     * @param int    $itemId
     * @param array  $info
     * @return \Magento\Framework\Object|false
     *
     * @see _getListItemInfo() for return format
     */
    protected function _getInfoForListItem($listType, $itemId, $info)
    {
        $productId = null;
        $buyRequest = new \Magento\Framework\Object();
        switch ($listType) {
            case \Magento\AdvancedCheckout\Block\Adminhtml\Sku\AbstractSku::LIST_TYPE:
                $info['sku'] = $itemId;
            // fall-through is intentional
            case \Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\AbstractErrors::LIST_TYPE:
                if (!isset($info['sku']) || (string)$info['sku'] == '') {
                    // Allow SKU == '0'
                    return false;
                }
                $item = $this->getCartModel()->prepareAddProductBySku($info['sku'], $info['qty'], $info);
                if ($item['code'] != \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_SUCCESS) {
                    return false;
                }
                $productId = $item['item']['id'];
                break;

            default:
                return $this->_getListItemInfo($listType, $itemId);
        }
        return new \Magento\Framework\Object(['product_id' => $productId, 'buy_request' => $buyRequest]);
    }

    /**
     * Returns item info by list and list item id
     * Returns object on success or false on error. Returned object has following keys:
     *  - product_id - null if no item found
     *  - buy_request - \Magento\Framework\Object, empty if not buy request stored for this item
     *
     * @param string $listType
     * @param int    $itemId
     * @return \Magento\Framework\Object
     */
    protected function _getListItemInfo($listType, $itemId)
    {
        $productId = null;
        $buyRequest = new \Magento\Framework\Object();
        switch ($listType) {
            case 'wishlist':
                $item = $this->_objectManager->create(
                    'Magento\Wishlist\Model\Item'
                )->loadWithOptions(
                    $itemId,
                    'info_buyRequest'
                );
                if ($item->getId()) {
                    $productId = $item->getProductId();
                    $buyRequest = $item->getBuyRequest();
                }
                break;
            case 'ordered':
                $item = $this->_objectManager->create('Magento\Sales\Model\Order\Item')->load($itemId);
                if ($item->getId()) {
                    $productId = $item->getProductId();
                    $buyRequest = $item->getBuyRequest();
                }
                break;
            default:
                $productId = (int)$itemId;
                break;
        }

        return new \Magento\Framework\Object(['product_id' => $productId, 'buy_request' => $buyRequest]);
    }

    /**
     * Process buyRequest file options of items
     *Magento\AdvancedCheckout\Controller\Adminhtml\Index
     * @param  array $items
     * @return array
     */
    protected function _processFiles($items)
    {
        /* @var $productHelper \Magento\Catalog\Helper\Product */
        $productHelper = $this->_objectManager->get('Magento\Catalog\Helper\Product');
        foreach ($items as $id => $item) {
            $buyRequest = new \Magento\Framework\Object($item);
            $params = ['files_prefix' => 'item_' . $id . '_'];
            $buyRequest = $productHelper->addParamsToBuyRequest($buyRequest, $params);
            if ($buyRequest->hasData()) {
                $items[$id] = $buyRequest->toArray();
            }
        }
        return $items;
    }
}
