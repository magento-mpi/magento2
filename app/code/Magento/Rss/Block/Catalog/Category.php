<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review form block
 */
class Magento_Rss_Block_Catalog_Category extends Magento_Rss_Block_Catalog_Abstract
{
    /**
     * @var Magento_Catalog_Model_Layer
     */
    protected $_catalogLayer;

    /**
     * @var Magento_Catalog_Model_Product_Visibility
     */
    protected $_visibility;

    /**
     * @var Magento_Rss_Model_RssFactory
     */
    protected $_rssFactory;

    /**
     * @var Magento_Catalog_Model_CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var Magento_Catalog_Model_Resource_Product_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Catalog_Model_Layer $catalogLayer
     * @param Magento_Catalog_Model_Product_Visibility $visibility
     * @param Magento_Rss_Model_RssFactory $rssFactory
     * @param Magento_Catalog_Model_CategoryFactory $categoryFactory
     * @param Magento_Catalog_Model_Resource_Product_CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Customer_Model_Session $customerSession,
        Magento_Catalog_Model_Layer $catalogLayer,
        Magento_Catalog_Model_Product_Visibility $visibility,
        Magento_Rss_Model_RssFactory $rssFactory,
        Magento_Catalog_Model_CategoryFactory $categoryFactory,
        Magento_Catalog_Model_Resource_Product_CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_catalogLayer = $catalogLayer;
        $this->_visibility = $visibility;
        $this->_rssFactory = $rssFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($catalogData, $coreData, $context, $storeManager, $customerSession, $data);
    }

    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_category_'
            . $this->getRequest()->getParam('cid') . '_'
            . $this->getRequest()->getParam('store_id') . '_'
            . $this->_customerSession->getId()
        );
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        $categoryId = $this->getRequest()->getParam('cid');
        $storeId = $this->_getStoreId();
        /** @var $rssModel Magento_Rss_Model_Rss */
        $rssModel = $this->_rssFactory->create();
        if ($categoryId) {
            $category = $this->_categoryFactory->create();
            $category->load($categoryId);
            if ($category && $category->getId()) {
                /** @var $layer Magento_Catalog_Model_Layer */
                $layer = $this->_catalogLayer->setStore($storeId);
                //want to load all products no matter anchor or not
                $category->setIsAnchor(true);
                $newUrl = $category->getUrl();
                $title = $category->getName();
                $rssModel->_addHeader(array(
                    'title'       => $title,
                    'description' => $title,
                    'link'        => $newUrl,
                    'charset'     => 'UTF-8',
                ));

                $_collection = $category->getCollection();
                $_collection->addAttributeToSelect('url_key')
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('is_anchor')
                    ->addAttributeToFilter('is_active',1)
                    ->addIdFilter($category->getChildren())
                    ->load()
                ;
                /** @var $productCollection Magento_Catalog_Model_Resource_Product_Collection */
                $productCollection = $this->_collectionFactory->create();

                $currentCategory = $layer->setCurrentCategory($category);
                $layer->prepareProductCollection($productCollection);
                $productCollection->addCountToCategories($_collection);

                $category->getProductCollection()->setStoreId($storeId);
                /*
                only load latest 50 products
                */
                $_productCollection = $currentCategory
                    ->getProductCollection()
                    ->addAttributeToSort('updated_at','desc')
                    ->setVisibility($this->_visibility->getVisibleInCatalogIds())
                    ->setCurPage(1)
                    ->setPageSize(50)
                ;

                if ($_productCollection->getSize() > 0) {
                    $args = array('rssObj' => $rssModel);
                    foreach ($_productCollection as $_product) {
                        $args['product'] = $_product;
                        $this->addNewItemXmlCallback($args);
                    }
                }
            }
        }
        return $rssModel->createRssXml();
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     */
    public function addNewItemXmlCallback($args)
    {
        /** @var $product Magento_Catalog_Model_Product */
        $product = $args['product'];
        $product->setAllowedInRss(true);
        $product->setAllowedPriceInRss(true);

        $this->_eventManager->dispatch('rss_catalog_category_xml_callback', $args);

        if (!$product->getAllowedInRss()) {
            return;
        }

        $description = '<table><tr>'
                     . '<td><a href="'.$product->getProductUrl().'"><img src="'
                     . $this->helper('Magento_Catalog_Helper_Image')->init($product, 'thumbnail')->resize(75, 75)
                     . '" border="0" align="left" height="75" width="75"></a></td>'
                     . '<td  style="text-decoration:none;">' . $product->getDescription();

        if ($product->getAllowedPriceInRss()) {
            $description.= $this->getPriceHtml($product,true);
        }

        $description .= '</td></tr></table>';
        /** @var $rssObj Magento_Rss_Model_Rss */
        $rssObj = $args['rssObj'];
        $data = array(
            'title'       => $product->getName(),
            'link'        => $product->getProductUrl(),
            'description' => $description,
        );

        $rssObj->_addEntry($data);
    }
}
