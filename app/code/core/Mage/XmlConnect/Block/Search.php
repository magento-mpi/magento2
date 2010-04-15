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
    /**
     * Search results xml renderer
     * XML also contains filters that can be apply (accorfingly already applyed filters and search query)
     * and sort fields
     *
     * @return string
     */
    protected function _toHtml()
    {
        $searchXmlObject  = new Varien_Simplexml_Element('<search></search>');
        $filtersXmlObject = new Varien_Simplexml_Element('<filters></filters>');

        /**
         * Filters apply and retrieving them
         */
        $searchEngine   = Mage::helper('catalogsearch')->getEngine();
        $request        = $this->getRequest();
        $requestParams  = $request->getParams();

        if ($searchEngine->isLeyeredNavigationAllowed()) {
            $filters    = array();
            $layer      = Mage::getSingleton('catalogsearch/layer');
            $attributes = $layer->getFilterableAttributes();

            /**
             * Apply and save filters
             */
            foreach ($attributes as $attributeItem) {
                $attributeCode  = $attributeItem->getAttributeCode();
                $filterModel    = $this->_getFilterByKey($attributeCode);

                $filterModel->setLayer($layer)
                    ->setAttributeModel($attributeItem);

                $filterParam = Mage_XmlConnect_Block_Filters::REQUEST_FILTER_PARAM_REFIX . $attributeCode;
                /**
                 * Set new request var
                 */
                if (isset($requestParams[$filterParam])) {
                    $filterModel->setRequestVar($filterParam);
                }
                $filterModel->apply($request, null);

                $filters[] = $filterModel;
            }

            /**
             * Separately apply and save category filter
             */
            $categoryFilter = $this->_getFilterByKey('category');
            $filterParam    = Mage_XmlConnect_Block_Filters::REQUEST_FILTER_PARAM_REFIX . $categoryFilter->getRequestVar();
            $categoryFilter->setLayer($layer)
                ->setRequestVar($filterParam)
                ->apply($this->getRequest(), null);

            $filters[] = $categoryFilter;

            /**
             * Render filters xml
             */
            foreach ($filters as $filter) {
                if (!$this->_isFilterItemsHasValues($filter)) {
                    continue;
                }
                $item = $filtersXmlObject->addChild('item');
                $item->addChild('name', $searchXmlObject->xmlentities($filter->getName()));
                $item->addChild('code', $filter->getRequestVar());
                $values = $item->addChild('values');

                foreach ($filter->getItems() as $valueItem) {
                    $count = (int)$valueItem->getCount();
                    if (!$count) {
                        continue;
                    }
                    $value = $values->addChild('value');
                    $value->addChild('id', $valueItem->getValueString());
                    $value->addChild('label', $searchXmlObject->xmlentities(strip_tags($valueItem->getLabel())));
                    $value->addChild('count', $count);
                }
            }
        }

        /**
         * Products
         */
        $layer      = Mage::getSingleton('catalogsearch/layer');
        $collection = $layer->getProductCollection();

        /**
         * Add rating and review summary, image attribute
         */
        $this->_prepareCollection($collection);

        /**
         * Apply sort order
         */
        $this->_addOrdersToProductCollection($collection, $request);

        /**
         * Apply offset and count
         */
        $collection->getSelect()->limit($request->getParam('count', 0), $request->getParam('offset', 0));

        $productsXml = $this->productCollectionToXml($collection, 'products', false, false, false, null, null);

        $searchXmlObject->appendChild($filtersXmlObject);
        /**
         * Sorting options
         */
        $xmlObject   = $this->getProductSortFeildsXmlObject();
        $searchXmlObject->appendChild($xmlObject);
        $xmlObject   = new Varien_Simplexml_Element($productsXml);
        $searchXmlObject->appendChild($xmlObject);

        return $searchXmlObject->asNiceXml();
    }

    /**
     * Check if items of specified filter have values
     *
     * @param object $filter filter model
     * @return bool
     */
    protected function _isFilterItemsHasValues($filter)
    {
        if (!$filter->getItemsCount()) {
            return false;
        }
        foreach ($filter->getItems() as $valueItem) {
            if ((int)$valueItem->getCount()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add ratting ans review summary, image attribute to product collection
     *
     * @param Varien_Data_Collection_Db $collection
     * @return Mage_XmlConnect_Block_Search
     */
    protected function _prepareCollection($collection)
    {
        $collection->joinField('rating_summary',
                         'review_entity_summary',
                         'rating_summary',
                         'entity_pk_value=entity_id',
                         array('entity_type'=>1, 'store_id'=> Mage::app()->getStore()->getId()),
                         'left')
            ->joinField('reviews_count',
                         'review_entity_summary',
                         'reviews_count',
                         'entity_pk_value=entity_id',
                         array('entity_type'=>1, 'store_id'=> Mage::app()->getStore()->getId()),
                         'left')
            ->addAttributeToSelect(array('image', 'name'));
        return $this;
    }
}
