<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enterprise Checkout Helper
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Items for requiring attention grid (doesn't include sku-failed items)
     *
     * @var null|array
     */
    protected $_items;

    /**
     * Items for requiring attention grid (including sku-failed items)
     *
     * @var null|array
     */
    protected $_itemsAll;

    /**
     * Config path to Enable Order By SKU tab in the Customer account dashboard and Allowed groups
     */
    const XML_PATH_SKU_ENABLED = 'sales/product_sku/my_account_enable';
    const XML_PATH_SKU_ALLOWED_GROUPS = 'sales/product_sku/allowed_groups';

    /**
     * Status of item, that was added by SKU
     */
    const ADD_ITEM_STATUS_SUCCESS = 'success';
    const ADD_ITEM_STATUS_FAILED_SKU = 'failed_sku';
    const ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK = 'failed_out_of_stock';
    const ADD_ITEM_STATUS_FAILED_QTY_ALLOWED = 'failed_qty_allowed';
    const ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART = 'failed_qty_allowed_in_cart';
    const ADD_ITEM_STATUS_FAILED_QTY_INVALID_NUMBER = 'failed_qty_invalid_number';
    const ADD_ITEM_STATUS_FAILED_QTY_INVALID_NON_POSITIVE = 'failed_qty_invalid_non_positive';
    const ADD_ITEM_STATUS_FAILED_QTY_INVALID_RANGE = 'failed_qty_invalid_range';
    const ADD_ITEM_STATUS_FAILED_QTY_INCREMENTS = 'failed_qty_increment';
    const ADD_ITEM_STATUS_FAILED_CONFIGURE = 'failed_configure';
    const ADD_ITEM_STATUS_FAILED_PERMISSIONS = 'failed_permissions';
    const ADD_ITEM_STATUS_FAILED_WEBSITE = 'failed_website';
    const ADD_ITEM_STATUS_FAILED_UNKNOWN = 'failed_unknown';
    const ADD_ITEM_STATUS_FAILED_EMPTY = 'failed_empty';
    const ADD_ITEM_STATUS_FAILED_DISABLED = 'failed_disabled';

    /**
     * Request parameter name, which indicates, whether file was uploaded
     */
    const REQUEST_PARAMETER_SKU_FILE_IMPORTED_FLAG = 'sku_file_uploaded';

    /**
     * Customer Groups that allow Order by SKU
     *
     * @var array|null
     */
    protected $_allowedGroups;

    /**
     * Contains session object to which data is saved
     *
     * @var Magento_Core_Model_Session_Abstract
     */
    protected $_session;

    /**
     * List of item statuses, that should be rendered by 'failed' template
     *
     * @var array
     */
    protected $_failedTemplateStatusCodes = array(
        self::ADD_ITEM_STATUS_FAILED_SKU,
        self::ADD_ITEM_STATUS_FAILED_PERMISSIONS
    );

    /**
     * Return session for affected items
     *
     * @return Magento_Core_Model_Session_Abstract
     */
    public function getSession()
    {
        if (!$this->_session) {
            $sessionClassPath = Mage::app()->getStore()->isAdmin() ?
                    'Magento_Adminhtml_Model_Session' : 'Magento_Customer_Model_Session';
            $this->_session =  Mage::getSingleton($sessionClassPath);
        }

        return $this->_session;
    }

    /**
     * Sets session instance to use for saving data
     *
     * @param Magento_Core_Model_Session_Abstract $session
     */
    public function setSession(Magento_Core_Model_Session_Abstract $session)
    {
        $this->_session = $session;
    }

    /**
     * Retrieve error message for the item
     *
     * @param Magento_Object $item
     * @return string
     */
    public function getMessageByItem(Magento_Object $item)
    {
        $message = $this->getMessage($item->getCode());
        return $message ? $message : $item->getError();
    }

    /**
     * Retrieve message by specified error code
     *
     * @param string $code
     * @return string
     */
    public function getMessage($code)
    {
        switch ($code) {
            case self::ADD_ITEM_STATUS_FAILED_SKU:
                $message = __('SKU not found in catalog.');
                break;
            case self::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK:
                $message = __('Availability: Out of stock.');
                break;
            case self::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED:
                $message = __('The requested quantity is not available.');
                break;
            case self::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART:
                $message = __('The product cannot be added to cart in requested quantity.');
                break;
            case self::ADD_ITEM_STATUS_FAILED_CONFIGURE:
                $message = __("Please specify the product's options.");
                break;
            case self::ADD_ITEM_STATUS_FAILED_PERMISSIONS:
                $message = __('The product cannot be added to cart.');
                break;
            case self::ADD_ITEM_STATUS_FAILED_QTY_INVALID_NUMBER:
            case self::ADD_ITEM_STATUS_FAILED_QTY_INVALID_NON_POSITIVE:
            case self::ADD_ITEM_STATUS_FAILED_QTY_INVALID_RANGE:
                $message = __('Please enter a valid number in the "Qty" field.');
                break;
            case self::ADD_ITEM_STATUS_FAILED_WEBSITE:
                $message = __('The product is assigned to another website.');
                break;
            case self::ADD_ITEM_STATUS_FAILED_DISABLED:
                $message = __('You can add only enabled products.');
                break;
            default:
                $message = '';
        }
        return $message;
    }

    /**
     * Check whether module enabled
     *
     * @return bool
     */
    public function isSkuEnabled()
    {
        $storeData = Mage::getStoreConfig(self::XML_PATH_SKU_ENABLED);
        return Magento_AdvancedCheckout_Model_Cart_Sku_Source_Settings::NO_VALUE != $storeData;
    }

    /**
     * Check whether Order by SKU functionality applicable to the current customer
     *
     * @return bool
     */
    public function isSkuApplied()
    {
        $result = false;
        $data = Mage::getStoreConfig(self::XML_PATH_SKU_ENABLED);
        switch ($data) {
            case Magento_AdvancedCheckout_Model_Cart_Sku_Source_Settings::YES_VALUE:
                $result = true;
                break;
            case Magento_AdvancedCheckout_Model_Cart_Sku_Source_Settings::YES_SPECIFIED_GROUPS_VALUE:
                /** @var $customerSession Magento_Customer_Model_Session */
                $customerSession = Mage::getSingleton('Magento_Customer_Model_Session');
                if ($customerSession) {
                    $groupId = $customerSession->getCustomerGroupId();
                    $result = $groupId === Magento_Customer_Model_Group::NOT_LOGGED_IN_ID
                        || in_array($groupId, $this->getSkuCustomerGroups());
                }
                break;
        }
        return $result;
    }

    /**
     * Retrieve Customer Groups that allow Order by SKU from config
     *
     * @return array
     */
    public function getSkuCustomerGroups()
    {
        if ($this->_allowedGroups === null) {
            $this->_allowedGroups = explode(',', trim(Mage::getStoreConfig(self::XML_PATH_SKU_ALLOWED_GROUPS)));
        }
        return $this->_allowedGroups;
    }

    /**
     * Get add by SKU failed items
     *
     * @param bool $all whether sku-failed items should be retrieved
     * @return array
     */
    public function getFailedItems($all = true)
    {
        if ($all && is_null($this->_itemsAll) || !$all && is_null($this->_items)) {
            $failedItems = Mage::getSingleton('Magento_AdvancedCheckout_Model_Cart')->getFailedItems();
            $collection = Mage::getResourceSingleton('Magento_AdvancedCheckout_Model_Resource_Product_Collection')
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect(Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes())
                ->addUrlRewrite();
            $itemsToLoad = array();

            $quoteItemsCollection = is_null($this->_items) ? array() : $this->_items;

            foreach ($failedItems as $item) {
                if (is_null($this->_items) && !in_array($item['code'], $this->_failedTemplateStatusCodes)) {
                    $id = $item['item']['id'];
                    if (!isset($itemsToLoad[$id])) {
                        $itemsToLoad[$id] = array();
                    }
                    $itemToLoad = $item['item'];
                    $itemToLoad['code'] = $item['code'];
                    $itemToLoad['error'] = isset($item['item']['error']) ? $item['item']['error'] : '';
                    // Avoid collisions of product ID with quote item ID
                    unset($itemToLoad['id']);
                    $itemsToLoad[$id][] = $itemToLoad;
                } elseif ($all && in_array($item['code'], $this->_failedTemplateStatusCodes)) {
                    $item['item']['code'] = $item['code'];
                    $item['item']['product_type'] = 'undefined';
                    // Create empty quote item. Otherwise it won't be correctly treated inside failed.phtml
                    $collectionItem = Mage::getModel('Magento_Sales_Model_Quote_Item')
                        ->setProduct(Mage::getModel('Magento_Catalog_Model_Product'))
                        ->addData($item['item']);
                    $quoteItemsCollection[] = $collectionItem;
                }
            }
            $ids = array_keys($itemsToLoad);
            if ($ids) {
                $collection->addIdFilter($ids);

                $quote = Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();
                $emptyQuoteItem = Mage::getModel('Magento_Sales_Model_Quote_Item');

                /** @var $itemProduct Magento_Catalog_Model_Product */
                foreach ($collection->getItems() as $product) {
                    $itemsCount = count($itemsToLoad[$product->getId()]);
                    foreach ($itemsToLoad[$product->getId()] as $index => $itemToLoad) {
                        $itemProduct = ($index == $itemsCount - 1) ? $product : (clone $product);
                        $itemProduct->addData($itemToLoad);
                        if (!$itemProduct->getOptionsByCode()) {
                            $itemProduct->setOptionsByCode(array());
                        }
                        // Create a new quote item and import data to it
                        $quoteItem = clone $emptyQuoteItem;
                        $quoteItem->addData($itemProduct->getData())
                            ->setQuote($quote)
                            ->setProduct($itemProduct)
                            ->setOptions($itemProduct->getOptions())
                            ->setRedirectUrl($itemProduct->getUrlModel()->getUrl($itemProduct));

                        $itemProduct->setCustomOptions($itemProduct->getOptionsByCode());
                        if (Mage::helper('Magento_Catalog_Helper_Data')->canApplyMsrp($itemProduct)) {
                            $quoteItem->setCanApplyMsrp(true);
                            $itemProduct->setRealPriceHtml(
                                Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice(
                                    Mage::helper('Magento_Tax_Helper_Data')->getPrice($itemProduct, $itemProduct->getFinalPrice(), true)
                                ))
                            );
                            $itemProduct->setAddToCartUrl(Mage::helper('Magento_Checkout_Helper_Cart')->getAddUrl($itemProduct));
                        } else {
                            $quoteItem->setCanApplyMsrp(false);
                        }

                        /** @var $stockItem Magento_CatalogInventory_Model_Stock_Item */
                        $stockItem = Mage::getModel('Magento_CatalogInventory_Model_Stock_Item');
                        $stockItem->assignProduct($itemProduct);
                        $quoteItem->setStockItem($stockItem);

                        $quoteItemsCollection[] = $quoteItem;
                    }
                }
            }

            if ($all) {
                $this->_itemsAll = $quoteItemsCollection;
            } else {
                $this->_items = $quoteItemsCollection;
            }
        }
        return $all ? $this->_itemsAll : $this->_items;
    }

    /**
     * Get text of general error while file uploading
     *
     * @return string
     */
    public function getFileGeneralErrorText()
    {
        return __('You cannot upload this file.');
    }

    /**
     * Process SKU file uploading and get uploaded data
     *
     * @param Magento_Core_Model_Session_Abstract|null $session
     * @return array|bool
     */
    public function processSkuFileUploading($session)
    {
        /** @var $importModel Magento_AdvancedCheckout_Model_Import */
        $importModel = Mage::getModel('Magento_AdvancedCheckout_Model_Import');
        try {
            $importModel->uploadFile();
            $rows = $importModel->getRows();
            if (empty($rows)) {
                Mage::throwException(__('The file is empty.'));
            }
            return $rows;
        } catch (Magento_Core_Exception $e) {
            if (!is_null($session)) {
                $session->addError($e->getMessage());
            }
        } catch (Exception $e) {
            if (!is_null($session)) {
                $session->addException($e, $this->getFileGeneralErrorText());
            }
        }
    }

    /**
     * Check whether SKU file was uploaded
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @return bool
     */
    public function isSkuFileUploaded(Magento_Core_Controller_Request_Http $request)
    {
        return (bool)$request->getPost(self::REQUEST_PARAMETER_SKU_FILE_IMPORTED_FLAG);
    }

    /**
     * Get url of account SKU tab
     *
     * @return string
     */
    public function getAccountSkuUrl()
    {
        return Mage::getSingleton('Magento_Core_Model_Url')->getUrl('enterprise_checkout/sku');
    }

    /**
     * Get text of message in case of empty SKU data error
     *
     * @return string
     */
    public function getSkuEmptyDataMessageText()
    {
        return $this->isSkuApplied()
            ? __('You have not entered a product SKU. Please <a href="%1">click here</a> to add product(s) by SKU.', $this->getAccountSkuUrl())
            : __('You have not entered a product SKU.');
    }
}
