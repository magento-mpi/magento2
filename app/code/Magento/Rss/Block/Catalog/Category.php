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
namespace Magento\Rss\Block\Catalog;

class Category extends \Magento\Rss\Block\Catalog\AbstractCatalog
{
    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @var \Magento\Rss\Model\RssFactory
     */
    protected $_rssFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Catalog\Model\Layer $catalogLayer
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Rss\Model\RssFactory $rssFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Catalog\Model\Layer $catalogLayer,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Rss\Model\RssFactory $rssFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        array $data = array()
    ) {
        $this->_imageHelper = $imageHelper;
        $this->_catalogLayer = $catalogLayer;
        $this->_visibility = $visibility;
        $this->_rssFactory = $rssFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $customerSession, $catalogData, $data);
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
        /** @var $rssModel \Magento\Rss\Model\Rss */
        $rssModel = $this->_rssFactory->create();
        if ($categoryId) {
            $category = $this->_categoryFactory->create();
            $category->load($categoryId);
            if ($category && $category->getId()) {
                /** @var $layer \Magento\Catalog\Model\Layer */
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
                /** @var $productCollection \Magento\Catalog\Model\Resource\Product\Collection */
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
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $args['product'];
        $product->setAllowedInRss(true);
        $product->setAllowedPriceInRss(true);

        $this->_eventManager->dispatch('rss_catalog_category_xml_callback', $args);

        if (!$product->getAllowedInRss()) {
            return;
        }

        $description = '<table><tr>'
                     . '<td><a href="'.$product->getProductUrl().'"><img src="'
                     . $this->_imageHelper->init($product, 'thumbnail')->resize(75, 75)
                     . '" border="0" align="left" height="75" width="75"></a></td>'
                     . '<td  style="text-decoration:none;">' . $product->getDescription();

        if ($product->getAllowedPriceInRss()) {
            $description.= $this->getPriceHtml($product,true);
        }

        $description .= '</td></tr></table>';
        /** @var $rssObj \Magento\Rss\Model\Rss */
        $rssObj = $args['rssObj'];
        $data = array(
            'title'       => $product->getName(),
            'link'        => $product->getProductUrl(),
            'description' => $description,
        );

        $rssObj->_addEntry($data);
    }
}
