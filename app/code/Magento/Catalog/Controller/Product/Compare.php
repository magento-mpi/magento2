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
 * @SuppressWarnings(PHPMD.LongVariable)
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
     * Catalog session
     *
     * @var Magento_Catalog_Model_Session
     */
    protected $_catalogSession;

    /**
     * Catalog product compare list
     *
     * @var Magento_Catalog_Model_Product_Compare_List
     */
    protected $_catalogProductCompareList;

    /**
     * Log visitor
     *
     * @var Magento_Log_Model_Visitor
     */
    protected $_logVisitor;

    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Item collection factory
     *
     * @var Magento_Catalog_Model_Resource_Product_Compare_Item_CollectionFactory
     */
    protected $_itemCollectionFactory;

    /**
     * Product factory
     *
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * Compare item factory
     *
     * @var Magento_Catalog_Model_Product_Compare_ItemFactory
     */
    protected $_compareItemFactory;

    /**
     * Customer factory
     *
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Construct
     *
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Catalog_Model_Product_Compare_ItemFactory $compareItemFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Catalog_Model_Resource_Product_Compare_Item_CollectionFactory $itemCollectionFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Log_Model_Visitor $logVisitor
     * @param Magento_Catalog_Model_Product_Compare_List $catalogProductCompareList
     * @param Magento_Catalog_Model_Session $catalogSession
     * @param Magento_Core_Controller_Varien_Action_Context $context
     */
    public function __construct(
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Catalog_Model_Product_Compare_ItemFactory $compareItemFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Catalog_Model_Resource_Product_Compare_Item_CollectionFactory $itemCollectionFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Customer_Model_Session $customerSession,
        Magento_Log_Model_Visitor $logVisitor,
        Magento_Catalog_Model_Product_Compare_List $catalogProductCompareList,
        Magento_Catalog_Model_Session $catalogSession,
        Magento_Core_Controller_Varien_Action_Context $context
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_compareItemFactory = $compareItemFactory;
        $this->_productFactory = $productFactory;
        $this->_itemCollectionFactory = $itemCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_logVisitor = $logVisitor;
        $this->_catalogProductCompareList = $catalogProductCompareList;
        $this->_catalogSession = $catalogSession;
        parent::__construct($context);
    }

    /**
     * Compare index action
     */
    public function indexAction()
    {
        $items = $this->getRequest()->getParam('items');

        $beforeUrl = $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED);
        if ($beforeUrl) {
            $this->_catalogSession
                ->setBeforeCompareUrl($this->_objectManager->get('Magento_Core_Helper_Data')->urlDecode($beforeUrl));
        }

        if ($items) {
            $items = explode(',', $items);
            /** @var Magento_Catalog_Model_Product_Compare_List $list */
            $list = $this->_catalogProductCompareList;
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
            && ($this->_logVisitor->getId()
                || $this->_customerSession->isLoggedIn())
        ) {
            /** @var Magento_Catalog_Model_Product $product */
            $product = $this->_productFactory->create();
            $product->setStoreId($this->_storeManager->getStore()->getId())
                ->load($productId);

            if ($product->getId()/* && !$product->isSuper()*/) {
                $this->_catalogProductCompareList->addProduct($product);
                $productName = $this->_objectManager->get('Magento_Core_Helper_Data')->escapeHtml($product->getName());
                $this->_catalogSession->addSuccess(
                    __('You added product %1 to the comparison list.', $productName)
                );
                $this->_eventManager->dispatch('catalog_product_compare_add_product', array('product'=>$product));
            }

            $this->_objectManager->get('Magento_Catalog_Helper_Product_Compare')->calculate();
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
            /** @var Magento_Catalog_Model_Product $product */
            $product = $this->_productFactory->create();
            $product->setStoreId($this->_storeManager->getStore()->getId())
                ->load($productId);

            if ($product->getId()) {
                /** @var $item Magento_Catalog_Model_Product_Compare_Item */
                $item = $this->_compareItemFactory->create();
                if ($this->_customerSession->isLoggedIn()) {
                    $item->addCustomerData($this->_customerSession->getCustomer());
                } elseif ($this->_customerId) {
                    $item->addCustomerData(
                        $this->_customerFactory->create()->load($this->_customerId)
                    );
                } else {
                    $item->addVisitorId($this->_logVisitor->getId());
                }

                $item->loadByProduct($product);
                /** @var $helper Magento_Catalog_Helper_Product_Compare */
                $helper = $this->_objectManager->get('Magento_Catalog_Helper_Product_Compare');
                if ($item->getId()) {
                    $item->delete();
                    $productName = $helper->escapeHtml($product->getName());
                    $this->_catalogSession->addSuccess(
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
        /** @var Magento_Catalog_Model_Resource_Product_Compare_Item_Collection $items */
        $items = $this->_itemCollectionFactory->create();

        if ($this->_customerSession->isLoggedIn()) {
            $items->setCustomerId($this->_customerSession->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId($this->_logVisitor->getId());
        }

        try {
            $items->clear();
            $this->_catalogSession->addSuccess(__('You cleared the comparison list.'));
            $this->_objectManager->get('Magento_Catalog_Helper_Product_Compare')->calculate();
        } catch (Magento_Core_Exception $e) {
            $this->_catalogSession->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_catalogSession->addException($e, __('Something went wrong  clearing the comparison list.'));
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
