<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Block\Catalog;

/**
 * Review form block
 */
class NewCatalog extends \Magento\Rss\Block\Catalog\AbstractCatalog
{
    /**
     * @var \Magento\Rss\Model\RssFactory
     */
    protected $_rssFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @var \Magento\Model\Resource\Iterator
     */
    protected $_resourceIterator;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Rss\Model\RssFactory $rssFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Model\Resource\Iterator $resourceIterator
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\App\Http\Context $httpContext,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Rss\Model\RssFactory $rssFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Model\Resource\Iterator $resourceIterator,
        \Magento\Catalog\Helper\Image $imageHelper,
        array $data = array()
    ) {
        $this->_imageHelper = $imageHelper;
        $this->_rssFactory = $rssFactory;
        $this->_productFactory = $productFactory;
        $this->_visibility = $visibility;
        $this->_resourceIterator = $resourceIterator;
        parent::__construct($context, $httpContext, $catalogData, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $storeId = $this->_getStoreId();
        $storeModel = $this->_storeManager->getStore($storeId);
        $newUrl = $this->_urlBuilder->getUrl('rss/catalog/new/store_id/' . $storeId);
        $title = __('New Products from %1', $storeModel->getFrontendName());
        $lang = $this->_scopeConfig->getValue(
            'general/locale/code',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeModel
        );

        /** @var $rssObj \Magento\Rss\Model\Rss */
        $rssObj = $this->_rssFactory->create();
        $rssObj->_addHeader(
            array(
                'title' => $title,
                'description' => $title,
                'link' => $newUrl,
                'charset' => 'UTF-8',
                'language' => $lang
            )
        );

        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_productFactory->create();
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(
            '00:00:00'
        )->toString(
            \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT
        );

        $todayEndOfDayDate = $this->_localeDate->date()->setTime(
            '23:59:59'
        )->toString(
            \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT
        );

        /** @var $products \Magento\Catalog\Model\Resource\Product\Collection */
        $products = $product->getCollection();
        $products->setStoreId($storeId);
        $products->addStoreFilter()->addAttributeToFilter(
            'news_from_date',
            array(
                'or' => array(
                    0 => array('date' => true, 'to' => $todayEndOfDayDate),
                    1 => array('is' => new \Zend_Db_Expr('null'))
                )
            ),
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            array(
                'or' => array(
                    0 => array('date' => true, 'from' => $todayStartOfDayDate),
                    1 => array('is' => new \Zend_Db_Expr('null'))
                )
            ),
            'left'
        )->addAttributeToFilter(
            array(
                array('attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')),
                array('attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null'))
            )
        )->addAttributeToSort(
            'news_from_date',
            'desc'
        )->addAttributeToSelect(
            array('name', 'short_description', 'description'),
            'inner'
        )->addAttributeToSelect(
            array(
                'price',
                'special_price',
                'special_from_date',
                'special_to_date',
                'msrp_enabled',
                'msrp_display_actual_price_type',
                'msrp',
                'thumbnail'
            ),
            'left'
        )->applyFrontendPriceLimitations();
        $products->setVisibility($this->_visibility->getVisibleInCatalogIds());

        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        $this->_resourceIterator->walk(
            $products->getSelect(),
            array(array($this, 'addNewItemXmlCallback')),
            array('rssObj' => $rssObj, 'product' => $product)
        );

        return $rssObj->createRssXml();
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     * @return void
     */
    public function addNewItemXmlCallback($args)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $args['product'];
        $product->setAllowedInRss(true);
        $product->setAllowedPriceInRss(true);
        $this->_eventManager->dispatch('rss_catalog_new_xml_callback', $args);

        if (!$product->getAllowedInRss()) {
            //Skip adding product to RSS
            return;
        }

        $allowedPriceInRss = $product->getAllowedPriceInRss();
        //$product->unsetData()->load($args['row']['entity_id']);
        $product->setData($args['row']);
        $description = '<table><tr>' .
            '<td><a href="' .
            $product->getProductUrl() .
            '"><img src="' .
            $this->_imageHelper->init(
                $product,
                'thumbnail'
            )->resize(
                75,
                75
            ) .
            '" border="0" align="left" height="75" width="75"></a></td>' .
            '<td  style="text-decoration:none;">' .
            $product->getDescription();

        if ($allowedPriceInRss) {
            $description .= $this->getPriceHtml($product, true);
            $description .= 'NEWPRICETEST: ' . $this->renderPriceHtml($product, true) . ' ENDNEWPRICETEST';
        }

        $description .= '</td>' . '</tr></table>';

        /** @var $rssObj \Magento\Rss\Model\Rss */
        $rssObj = $args['rssObj'];
        $rssObj->_addEntry(
            array('title' => $product->getName(), 'link' => $product->getProductUrl(), 'description' => $description)
        );
    }
}
