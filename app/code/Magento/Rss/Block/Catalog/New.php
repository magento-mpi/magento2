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
class Magento_Rss_Block_Catalog_New extends Magento_Rss_Block_Catalog_Abstract
{
    /**
     * @var Magento_Rss_Model_RssFactory
     */
    protected $_rssFactory;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Catalog_Model_Product_Visibility
     */
    protected $_visibility;

    /**
     * @var Magento_Core_Model_Resource_Iterator
     */
    protected $_resourceIterator;

    /**
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Rss_Model_RssFactory $rssFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Catalog_Model_Product_Visibility $visibility
     * @param Magento_Core_Model_Resource_Iterator $resourceIterator
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Customer_Model_Session $customerSession,
        Magento_Rss_Model_RssFactory $rssFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Catalog_Model_Product_Visibility $visibility,
        Magento_Core_Model_Resource_Iterator $resourceIterator,
        array $data = array()
    ) {
        $this->_rssFactory = $rssFactory;
        $this->_productFactory = $productFactory;
        $this->_locale = $locale;
        $this->_visibility = $visibility;
        $this->_resourceIterator = $resourceIterator;
        parent::__construct($catalogData, $coreData, $context, $storeManager, $customerSession, $data);
    }

    protected function _toHtml()
    {
        $storeId = $this->_getStoreId();
        $storeModel = $this->_storeManager->getStore($storeId);
        $newUrl = $this->_urlBuilder->getUrl('rss/catalog/new/store_id/' . $storeId);
        $title = __('New Products from %1', $storeModel->getFrontendName());
        $lang = $storeModel->getConfig('general/locale/code');

        /** @var $rssObj Magento_Rss_Model_Rss */
        $rssObj = $this->_rssFactory->create();
        $rssObj->_addHeader(array('title' => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
            'language'    => $lang
        ));

        /** @var $product Magento_Catalog_Model_Product */
        $product = $this->_productFactory->create();
        $todayStartOfDayDate  = $this->_locale->date()
            ->setTime('00:00:00')
            ->toString(Magento_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate  = $this->_locale->date()
            ->setTime('23:59:59')
            ->toString(Magento_Date::DATETIME_INTERNAL_FORMAT);

        /** @var $products Magento_Catalog_Model_Resource_Product_Collection */
        $products = $product->getCollection();
        $products->setStoreId($storeId);
        $products->addStoreFilter()
            ->addAttributeToFilter('news_from_date', array('or' => array(
                0 => array('date' => true, 'to' => $todayEndOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter('news_to_date', array('or' => array(
                0 => array('date' => true, 'from' => $todayStartOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter(
                array(
                    array('attribute' => 'news_from_date', 'is' => new Zend_Db_Expr('not null')),
                    array('attribute' => 'news_to_date', 'is' => new Zend_Db_Expr('not null'))
                )
            )
            ->addAttributeToSort('news_from_date','desc')
            ->addAttributeToSelect(array('name', 'short_description', 'description'), 'inner')
            ->addAttributeToSelect(
                array(
                    'price', 'special_price', 'special_from_date', 'special_to_date',
                    'msrp_enabled', 'msrp_display_actual_price_type', 'msrp', 'thumbnail'
                ),
                'left'
            )
            ->applyFrontendPriceLimitations()
        ;
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
     */
    public function addNewItemXmlCallback($args)
    {
        /** @var $product Magento_Catalog_Model_Product */
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
        $description = '<table><tr>'
            . '<td><a href="'.$product->getProductUrl().'"><img src="'
            . $this->helper('Magento_Catalog_Helper_Image')->init($product, 'thumbnail')->resize(75, 75)
            .'" border="0" align="left" height="75" width="75"></a></td>'.
            '<td  style="text-decoration:none;">'.$product->getDescription();

        if ($allowedPriceInRss) {
            $description .= $this->getPriceHtml($product, true);
        }

        $description .= '</td>' . '</tr></table>';

        /** @var $rssObj Magento_Rss_Model_Rss */
        $rssObj = $args['rssObj'];
        $rssObj->_addEntry(array(
            'title'       => $product->getName(),
            'link'        => $product->getProductUrl(),
            'description' => $description,
        ));
    }
}
