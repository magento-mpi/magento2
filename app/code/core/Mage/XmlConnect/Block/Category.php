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
 * Review form block
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Category extends Mage_XmlConnect_Block_Abstract
{

    protected function _toHtml()
    {
        $additionalAttributes = $this->getChildHtml();
        if ($categoryId = $this->getRequest()->getParam('category_id', null))
        {
            $categoryModel = Mage::getModel('catalog/category')->load($categoryId);
            /* Return products list if there are no child categories*/
            if (!$categoryModel->hasChildren())
            {
                $request        = $this->getRequest();
                $requestParams  = $request->getParams();
                $layer          = Mage::getSingleton('catalog/layer');
                $layer->setCurrentCategory($categoryModel);
                $attributes     = $layer->getFilterableAttributes();

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
                }

                /**
                 * Separately apply and save category filter
                 */
                $categoryFilter = $this->_getFilterByKey('category');
                $filterParam    = Mage_XmlConnect_Block_Filters::REQUEST_FILTER_PARAM_REFIX . $categoryFilter->getRequestVar();
                $categoryFilter->setLayer($layer)
                    ->setRequestVar($filterParam)
                    ->apply($this->getRequest(), null);

                /**
                 * Products
                 */
                $layer      = Mage::getSingleton('catalog/layer');
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

                return $this->productCollectionToXml($collection, 'category', true, false, false, $additionalAttributes, 'products');
            }
        }
        $categoryCollection = Mage::getResourceModel('xmlconnect/category_collection');
        $categoryCollection->addImageToResult()
            ->setStoreId($categoryCollection->getDefaultStoreId())
            ->addParentIdFilter($categoryId)
            ->addLimit($this->getRequest()->getParam('offset', 0), $this->getRequest()->getParam('count', 0));
        $xml = $this->categoryCollectionToXml($categoryCollection, 'category', true, false, false, $additionalAttributes);
        return $xml;
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
