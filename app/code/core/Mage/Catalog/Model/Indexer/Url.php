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
 * @category   Mage
 * @package    Mage_
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Catalog_Model_Indexer_Url extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * @var array
     */
    protected $_matchedEntities = array(
        Mage_Catalog_Model_Product::ENTITY => array(
                Mage_Index_Model_Event::TYPE_SAVE
            ),
        Mage_Catalog_Model_Category::ENTITY => array(
                Mage_Index_Model_Event::TYPE_SAVE
            )
    );

    /**
     * Get Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('catalog')->__('Catalog Url Rewrites');
    }

    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('catalog')->__('Index product and categories url rewrites');
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $entity = $event->getEntity();
        switch ($entity) {
            case Mage_Catalog_Model_Product::ENTITY:
               $this->_registerProductEvent($event);
            break;
            case Mage_Catalog_Model_Category::ENTITY:
                $this->_registerCategoryEvent($event);
            break;
        }
        return $this;
    }

    /**
     * Register event data during product save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerProductEvent(Mage_Index_Model_Event $event)
    {
        $product = $event->getDataObject();
        $dataChange = $product->dataHasChangedFor('url_key')
            || $product->getIsChangedCategories()
            || $product->getIsChangedWebsites();

        if (!$product->getExcludeUrlRewrite() && $dataChange) {
            $event->addNewData('rewrite_product_ids', array($product->getId()));
        }
    }

    /**
     * Register event data during category save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerCategoryEvent(Mage_Index_Model_Event $event)
    {
        $category = $event->getDataObject();
        if (!$category->getInitialSetupFlag() && $category->getLevel() > 1) {
            if ($category->dataHasChangedFor('url_key') || $category->getIsChangedProductList()) {
                $event->addNewData('rewrite_category_ids', array($category->getId()));
            }
            /**
             * Check if category has another affected category ids (category move result)
             */
            if ($category->getAffectedCategoryIds()) {
                $event->addNewData('rewrite_category_ids', $category->getAffectedCategoryIds());
            }
        }
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (isset($data['rewrite_product_ids'])) {
            foreach ($data['rewrite_product_ids'] as $productId) {
                 Mage::getSingleton('catalog/url')->refreshProductRewrite($productId);
            }
        }
        if (isset($data['rewrite_category_ids'])) {
            foreach ($data['rewrite_category_ids'] as $categoryId) {
                Mage::getSingleton('catalog/url')->refreshCategoryRewrite($categoryId);
            }
        }
    }

    /**
     * Rebuild all index data
     */
    public function reindexAll()
    {
        Mage::getSingleton('catalog/url')->refreshRewrites();
    }
}