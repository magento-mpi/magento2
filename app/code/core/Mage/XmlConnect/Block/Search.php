<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product search results renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Search extends Mage_XmlConnect_Block_Abstract
{


    protected function _toHtml()
    {

        /**
         * Sorting options
         */
        $sortOptions = Mage::getModel('catalog/category')->getAvailableSortByOptions();
        $sortOptions = array_slice($sortOptions, 0, 3);
        $sortingXml = '<orders>' . $this->_arrayToXml($sortOptions, null, 'item') . '</orders>';

        /**
         * Products
         */
        $engine = Mage::helper('catalogsearch')->getEngine();
        $collection = $engine->getResultCollection();

        $collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addSearchFilter(Mage::helper('catalogsearch')->getQuery()->getQueryText())
            ->setStore(Mage::app()->getStore());

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);


        $this->_addFiltersToProductCollection($collection, $this->getRequest());
        $this->_addOrdersToProductCollection($collection, $this->getRequest());

        $offset = $this->getRequest()->getParam('offset', 0);
        if ($offset <= 0) {
            $page = 1;
        }
        else {
            $page = ceil(($collection->getSize() + $offset) / $collection->getSize());
        }

        $collection->setPageSize($this->getRequest()->getParam('count', 0))
            ->setCurPage($page);

        $productsXml = $this->productCollectionToXml($collection, 'products', false, false, false, null, null);

        $searchXmlObject    = new Varien_Simplexml_Element('<search></search>');
        $xmlObject          = new Varien_Simplexml_Element($sortingXml);
        $searchXmlObject->appendChild($xmlObject);
        $xmlObject          = new Varien_Simplexml_Element($productsXml);
        $searchXmlObject->appendChild($xmlObject);

        return $searchXmlObject->asXML();
    }

}
