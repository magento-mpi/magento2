<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product rss feed builder
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Catalog_Product_Rss extends Magento_Rss_Block_Catalog_Abstract
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $tagModel = Mage::registry('tag_model');
        if ($tagModel) {
            $this->setCacheKey('rss_catalog_tag_' . $this->getStoreId() . '_' . $tagModel->getName());
        }
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        //store id is store view id
        $storeId = $this->_getStoreId();
        $tagModel = Mage::registry('tag_model');
        $newurl = Mage::getUrl('rss/catalog/tag/tagName/' . $tagModel->getName());
        $title = __('Products tagged with %1', $tagModel->getName());
        $lang = Mage::getStoreConfig('general/locale/code');

        $rssObj = Mage::getModel('Magento_Rss_Model_Rss');
        $data = array('title' => $title,
            'description' => $title,
            'link'        => $newurl,
            'charset'     => 'UTF-8',
            'language'    => $lang
        );
        $rssObj->_addHeader($data);

        $_collection = $tagModel->getEntityCollection()
            ->addTagFilter($tagModel->getId())
            ->addStoreFilter($storeId);

        $_collection->setVisibility(Mage::getSingleton('Magento_Catalog_Model_Product_Visibility')
            ->getVisibleInCatalogIds());

        $product = Mage::getModel('Magento_Catalog_Model_Product');

        Mage::getSingleton('Magento_Core_Model_Resource_Iterator')->walk(
            $_collection->getSelect(),
            array(array($this, 'addTaggedItemXml')),
            array('rssObj'=> $rssObj, 'product'=>$product),
            $_collection->getSelect()->getAdapter()
        );

        return $rssObj->createRssXml();
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     */
    public function addTaggedItemXml($args)
    {
        $product = $args['product'];

        $product->setAllowedInRss(true);
        $product->setAllowedPriceInRss(true);
        Mage::dispatchEvent('rss_catalog_tagged_item_xml_callback', $args);

        if (!$product->getAllowedInRss()) {
            //Skip adding product to RSS
            return;
        }

        $allowedPriceInRss = $product->getAllowedPriceInRss();

        $product->unsetData()->load($args['row']['entity_id']);
        $description = '<table><tr><td><a href="'.$product->getProductUrl().'">'
            . '<img src="' . $this->helper('Magento_Catalog_Helper_Image')->init($product, 'thumbnail')->resize(75, 75)
            . '" border="0" align="left" height="75" width="75"></a></td>'
            . '<td  style="text-decoration:none;">'.$product->getDescription();

        if ($allowedPriceInRss) {
            $description .= $this->getPriceHtml($product, true);
        }

        $description .='</td></tr></table>';

        $rssObj = $args['rssObj'];
        $data = array(
            'title'         => $product->getName(),
            'link'          => $product->getProductUrl(),
            'description'   => $description,
        );
        $rssObj->_addEntry($data);
    }
}
