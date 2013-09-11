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
namespace Magento\Rss\Block\Catalog;

class New extends \Magento\Rss\Block\Catalog\AbstractCatalog
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        //$this->setCacheKey('rss_catalog_new_'.$this->_getStoreId());
        //$this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        $storeId = $this->_getStoreId();

        $newurl = \Mage::getUrl('rss/catalog/new/store_id/' . $storeId);
        $title = __('New Products from %1', \Mage::getModel('\Magento\Core\Model\StoreManagerInterface')->getStore($storeId)->getFrontendName());
        $lang = \Mage::getStoreConfig('general/locale/code');

        $rssObj = \Mage::getModel('\Magento\Rss\Model\Rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                'language'    => $lang
                );
        $rssObj->_addHeader($data);
/*
oringinal price - getPrice() - inputed in admin
special price - getSpecialPrice()
getFinalPrice() - used in shopping cart calculations
*/

        $product = \Mage::getModel('\Magento\Catalog\Model\Product');

        $todayStartOfDayDate  = \Mage::app()->getLocale()->date()
            ->setTime('00:00:00')
            ->toString(\Magento\Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate  = \Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(\Magento\Date::DATETIME_INTERNAL_FORMAT);

        $products = $product->getCollection()
            ->setStoreId($storeId)
            ->addStoreFilter()
            ->addAttributeToFilter('news_from_date', array('or' => array(
                0 => array('date' => true, 'to' => $todayEndOfDayDate),
                1 => array('is' => new \Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter('news_to_date', array('or' => array(
                0 => array('date' => true, 'from' => $todayStartOfDayDate),
                1 => array('is' => new \Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter(
                array(
                    array('attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')),
                    array('attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null'))
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

        $products->setVisibility(\Mage::getSingleton('Magento\Catalog\Model\Product\Visibility')->getVisibleInCatalogIds());

        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */

        \Mage::getSingleton('Magento\Core\Model\Resource\Iterator')->walk(
                $products->getSelect(),
                array(array($this, 'addNewItemXmlCallback')),
                array('rssObj'=> $rssObj, 'product'=>$product)
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
        $product = $args['product'];

        $product->setAllowedInRss(true);
        $product->setAllowedPriceInRss(true);
        \Mage::dispatchEvent('rss_catalog_new_xml_callback', $args);

        if (!$product->getAllowedInRss()) {
            //Skip adding product to RSS
            return;
        }

        $allowedPriceInRss = $product->getAllowedPriceInRss();

        //$product->unsetData()->load($args['row']['entity_id']);
        $product->setData($args['row']);
        $description = '<table><tr>'
            . '<td><a href="'.$product->getProductUrl().'"><img src="'
            . $this->helper('\Magento\Catalog\Helper\Image')->init($product, 'thumbnail')->resize(75, 75)
            .'" border="0" align="left" height="75" width="75"></a></td>'.
            '<td  style="text-decoration:none;">'.$product->getDescription();

        if ($allowedPriceInRss) {
            $description .= $this->getPriceHtml($product,true);
        }

        $description .= '</td>'.
            '</tr></table>';

        $rssObj = $args['rssObj'];
        $data = array(
                'title'         => $product->getName(),
                'link'          => $product->getProductUrl(),
                'description'   => $description,
            );
        $rssObj->_addEntry($data);
    }
}
