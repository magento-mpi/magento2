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
 *
 * @category   Magento
 * @package    Magento_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rss_Block_Catalog_Category extends Magento_Rss_Block_Catalog_Abstract
{
    /**
     * Catalog image
     *
     * @var Magento_Catalog_Helper_Image
     */
    protected $_catalogImage = null;

    /**
     * @param Magento_Catalog_Helper_Image $catalogImage
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Image $catalogImage,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_catalogImage = $catalogImage;
        parent::__construct($catalogData, $context, $data);
    }

    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_category_'
            . $this->getRequest()->getParam('cid') . '_'
            . $this->getRequest()->getParam('store_id') . '_'
            . Mage::getModel('Magento_Customer_Model_Session')->getId()
        );
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        $categoryId = $this->getRequest()->getParam('cid');
        $storeId = $this->_getStoreId();
        $rssObj = Mage::getModel('Magento_Rss_Model_Rss');
        if ($categoryId) {
            $category = Mage::getModel('Magento_Catalog_Model_Category')->load($categoryId);
            if ($category && $category->getId()) {
                $layer = Mage::getSingleton('Magento_Catalog_Model_Layer')->setStore($storeId);
                //want to load all products no matter anchor or not
                $category->setIsAnchor(true);
                $newurl = $category->getUrl();
                $title = $category->getName();
                $data = array('title' => $title,
                        'description' => $title,
                        'link'        => $newurl,
                        'charset'     => 'UTF-8',
                        );

                $rssObj->_addHeader($data);

                $_collection = $category->getCollection();
                $_collection->addAttributeToSelect('url_key')
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('is_anchor')
                    ->addAttributeToFilter('is_active',1)
                    ->addIdFilter($category->getChildren())
                    ->load()
                ;
                $productCollection = Mage::getModel('Magento_Catalog_Model_Product')->getCollection();

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
                    ->setVisibility(Mage::getSingleton('Magento_Catalog_Model_Product_Visibility')->getVisibleInCatalogIds())
                    ->setCurPage(1)
                    ->setPageSize(50)
                ;

                if ($_productCollection->getSize()>0) {
                    $args = array('rssObj' => $rssObj);
                    foreach ($_productCollection as $_product) {
                        $args['product'] = $_product;
                        $this->addNewItemXmlCallback($args);
                    }
                }
            }
        }
        return $rssObj->createRssXml();
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     */
    public function addNewItemXmlCallback($args)
    {
        $product = $args['product'];
        $product->setAllowedInRss(true);
        $product->setAllowedPriceInRss(true);

        Mage::dispatchEvent('rss_catalog_category_xml_callback', $args);

        if (!$product->getAllowedInRss()) {
            return;
        }

        $description = '<table><tr>'
                     . '<td><a href="'.$product->getProductUrl().'"><img src="'
                     . $this->_catalogImage->init($product, 'thumbnail')->resize(75, 75)
                     . '" border="0" align="left" height="75" width="75"></a></td>'
                     . '<td  style="text-decoration:none;">' . $product->getDescription();

        if ($product->getAllowedPriceInRss()) {
            $description.= $this->getPriceHtml($product,true);
        }

        $description .= '</td></tr></table>';
        $rssObj = $args['rssObj'];
        $data = array(
                'title'         => $product->getName(),
                'link'          => $product->getProductUrl(),
                'description'   => $description,
            );

        $rssObj->_addEntry($data);
    }
}
