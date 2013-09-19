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
namespace Magento\AdvancedCheckout\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
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
     * @var \Magento\Core\Model\Session\AbstractSession
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
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData = null;

    /**
     * Checkout cart
     *
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_checkoutCart = null;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Checkout\Helper\Cart $checkoutCart
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\Checkout\Helper\Cart $checkoutCart,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig
    ) {
        $this->_checkoutCart = $checkoutCart;
        $this->_taxData = $taxData;
        $this->_catalogData = $catalogData;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Return session for affected items
     *
     * @return \Magento\Core\Model\Session\AbstractSession
     */
    public function getSession()
    {
        if (!$this->_session) {
            $sessionClassPath = \Mage::app()->getStore()->isAdmin() ?
                    'Magento\Adminhtml\Model\Session' : 'Magento\Customer\Model\Session';
            $this->_session =  \Mage::getSingleton($sessionClassPath);
        }

        return $this->_session;
    }

    /**
     * Sets session instance to use for saving data
     *
     * @param \Magento\Core\Model\Session\AbstractSession $session
     */
    public function setSession(\Magento\Core\Model\Session\AbstractSession $session)
    {
        $this->_session = $session;
    }

    /**
     * Retrieve error message for the item
     *
     * @param \Magento\Object $item
     * @return string
     */
    public function getMessageByItem(\Magento\Object $item)
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
        $storeData = $this->_coreStoreConfig->getConfig(self::XML_PATH_SKU_ENABLED);
        return \Magento\AdvancedCheckout\Model\Cart\Sku\Source\Settings::NO_VALUE != $storeData;
    }

    /**
     * Check whether Order by SKU functionality applicable to the current customer
     *
     * @return bool
     */
    public function isSkuApplied()
    {
        $result = false;
        $data = $this->_coreStoreConfig->getConfig(self::XML_PATH_SKU_ENABLED);
        switch ($data) {
            case \Magento\AdvancedCheckout\Model\Cart\Sku\Source\Settings::YES_VALUE:
                $result = true;
                break;
            case \Magento\AdvancedCheckout\Model\Cart\Sku\Source\Settings::YES_SPECIFIED_GROUPS_VALUE:
                /** @var $customerSession \Magento\Customer\Model\Session */
                $customerSession = \Mage::getSingleton('Magento\Customer\Model\Session');
                if ($customerSession) {
                    $groupId = $customerSession->getCustomerGroupId();
                    $result = $groupId === \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID
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
            $this->_allowedGroups = explode(
                ',', trim($this->_coreStoreConfig->getConfig(self::XML_PATH_SKU_ALLOWED_GROUPS))
            );
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
            $failedItems = \Mage::getSingleton('Magento\AdvancedCheckout\Model\Cart')->getFailedItems();
            $collection = \Mage::getResourceSingleton('Magento\AdvancedCheckout\Model\Resource\Product\Collection')
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect(\Mage::getSingleton('Magento\Catalog\Model\Config')->getProductAttributes())
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
                    $collectionItem = \Mage::getModel('Magento\Sales\Model\Quote\Item')
                        ->setProduct(\Mage::getModel('Magento\Catalog\Model\Product'))
                        ->addData($item['item']);
                    $quoteItemsCollection[] = $collectionItem;
                }
            }
            $ids = array_keys($itemsToLoad);
            if ($ids) {
                $collection->addIdFilter($ids);

                $quote = \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
                $emptyQuoteItem = \Mage::getModel('Magento\Sales\Model\Quote\Item');

                /** @var $itemProduct \Magento\Catalog\Model\Product */
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
                        if ($this->_catalogData->canApplyMsrp($itemProduct)) {
                            $quoteItem->setCanApplyMsrp(true);
                            $itemProduct->setRealPriceHtml(
                                \Mage::app()->getStore()->formatPrice(\Mage::app()->getStore()->convertPrice(
                                    $this->_taxData->getPrice($itemProduct, $itemProduct->getFinalPrice(), true)
                                ))
                            );
                            $itemProduct->setAddToCartUrl($this->_checkoutCart->getAddUrl($itemProduct));
                        } else {
                            $quoteItem->setCanApplyMsrp(false);
                        }

                        /** @var $stockItem \Magento\CatalogInventory\Model\Stock\Item */
                        $stockItem = \Mage::getModel('Magento\CatalogInventory\Model\Stock\Item');
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
     * @param \Magento\Core\Model\Session\AbstractSession|null $session
     * @return array|bool
     */
    public function processSkuFileUploading($session)
    {
        /** @var $importModel \Magento\AdvancedCheckout\Model\Import */
        $importModel = \Mage::getModel('Magento\AdvancedCheckout\Model\Import');
        try {
            $importModel->uploadFile();
            $rows = $importModel->getRows();
            if (empty($rows)) {
                \Mage::throwException(__('The file is empty.'));
            }
            return $rows;
        } catch (\Magento\Core\Exception $e) {
            if (!is_null($session)) {
                $session->addError($e->getMessage());
            }
        } catch (\Exception $e) {
            if (!is_null($session)) {
                $session->addException($e, $this->getFileGeneralErrorText());
            }
        }
    }

    /**
     * Check whether SKU file was uploaded
     *
     * @param \Magento\Core\Controller\Request\Http $request
     * @return bool
     */
    public function isSkuFileUploaded(\Magento\Core\Controller\Request\Http $request)
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
        return \Mage::getSingleton('Magento\Core\Model\Url')->getUrl('magento_advancedcheckout/sku');
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
