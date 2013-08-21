<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bundle Products Observer
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Model_Observer
{
    /**
     * Setting Bundle Items Data to product for father processing
     *
     * @param Magento_Object $observer
     * @return Magento_Bundle_Model_Observer
     */
    public function prepareProductSave($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();

        if (($items = $request->getPost('bundle_options')) && !$product->getCompositeReadonly()) {
            $product->setBundleOptionsData($items);
        }

        if (($selections = $request->getPost('bundle_selections')) && !$product->getCompositeReadonly()) {
            $product->setBundleSelectionsData($selections);
        }

        if ($product->getPriceType() == '0' && !$product->getOptionsReadonly()) {
            $product->setCanSaveCustomOptions(true);
            if ($customOptions = $product->getProductOptions()) {
                foreach (array_keys($customOptions) as $key) {
                    $customOptions[$key]['is_delete'] = 1;
                }
                $product->setProductOptions($customOptions);
            }
        }

        $product->setCanSaveBundleSelections(
            (bool)$request->getPost('affect_bundle_product_selections') && !$product->getCompositeReadonly()
        );

        return $this;
    }

    /**
     * Append bundles in upsell list for current product
     *
     * @param Magento_Object $observer
     * @return Magento_Bundle_Model_Observer
     */
    public function appendUpsellProducts($observer)
    {
        /* @var $product Magento_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();

        /**
         * Check is current product type is allowed for bundle selection product type
         */
        if (!in_array($product->getTypeId(), Mage::helper('Magento_Bundle_Helper_Data')->getAllowedSelectionTypes())) {
            return $this;
        }

        /* @var $collection Magento_Catalog_Model_Resource_Product_Link_Product_Collection */
        $collection = $observer->getEvent()->getCollection();
        $limit      = $observer->getEvent()->getLimit();
        if (is_array($limit)) {
            if (isset($limit['upsell'])) {
                $limit = $limit['upsell'];
            } else {
                $limit = 0;
            }
        }

        /* @var $resource Magento_Bundle_Model_Resource_Selection */
        $resource   = Mage::getResourceSingleton('Magento_Bundle_Model_Resource_Selection');

        $productIds = array_keys($collection->getItems());
        if (!is_null($limit) && $limit <= count($productIds)) {
            return $this;
        }

        // retrieve bundle product ids
        $bundleIds  = $resource->getParentIdsByChild($product->getId());
        // exclude up-sell product ids
        $bundleIds  = array_diff($bundleIds, $productIds);

        if (!$bundleIds) {
            return $this;
        }

        /* @var $bundleCollection Magento_Catalog_Model_Resource_Product_Collection */
        $bundleCollection = $product->getCollection()
            ->addAttributeToSelect(Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes())
            ->addStoreFilter()
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->setVisibility(Mage::getSingleton('Magento_Catalog_Model_Product_Visibility')->getVisibleInCatalogIds());

        if (!is_null($limit)) {
            $bundleCollection->setPageSize($limit);
        }
        $bundleCollection->addFieldToFilter('entity_id', array('in' => $bundleIds))
            ->setFlag('do_not_use_category_id', true);

        if ($collection instanceof Magento_Data_Collection) {
            foreach ($bundleCollection as $item) {
                $collection->addItem($item);
            }
        } elseif ($collection instanceof Magento_Object) {
            $items = $collection->getItems();
            foreach ($bundleCollection as $item) {
                $items[$item->getEntityId()] = $item;
            }
            $collection->setItems($items);
        }

        return $this;
    }

    /**
     * Append selection attributes to selection's order item
     *
     * @param Magento_Object $observer
     * @return Magento_Bundle_Model_Observer
     */
    public function appendBundleSelectionData($observer)
    {
        $orderItem = $observer->getEvent()->getOrderItem();
        $quoteItem = $observer->getEvent()->getItem();

        if ($attributes = $quoteItem->getProduct()->getCustomOption('bundle_selection_attributes')) {
            $productOptions = $orderItem->getProductOptions();
            $productOptions['bundle_selection_attributes'] = $attributes->getValue();
            $orderItem->setProductOptions($productOptions);
        }

        return $this;
    }

    /**
     * Add price index data for catalog product collection
     * only for front end
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Bundle_Model_Observer
     */
    public function loadProductOptions($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        /* @var $collection Magento_Catalog_Model_Resource_Product_Collection */
        $collection->addPriceData();

        return $this;
    }

    /**
     * duplicating bundle options and selections
     *
     * @param Magento_Object $observer
     * @return Magento_Bundle_Model_Observer
     */
    public function duplicateProduct($observer)
    {
        $product = $observer->getEvent()->getCurrentProduct();

        if ($product->getTypeId() != Magento_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            //do nothing if not bundle
            return $this;
        }

        $newProduct = $observer->getEvent()->getNewProduct();

        $product->getTypeInstance()->setStoreFilter($product->getStoreId(), $product);
        $optionCollection = $product->getTypeInstance()->getOptionsCollection($product);
        $selectionCollection = $product->getTypeInstance()->getSelectionsCollection(
            $product->getTypeInstance()->getOptionsIds($product),
            $product
        );
        $optionCollection->appendSelections($selectionCollection);

        $optionRawData = array();
        $selectionRawData = array();

        $i = 0;
        foreach ($optionCollection as $option) {
            $optionRawData[$i] = array(
                    'required' => $option->getData('required'),
                    'position' => $option->getData('position'),
                    'type' => $option->getData('type'),
                    'title' => $option->getData('title')?$option->getData('title'):$option->getData('default_title'),
                    'delete' => ''
                );
            foreach ($option->getSelections() as $selection) {
                $selectionRawData[$i][] = array(
                    'product_id' => $selection->getProductId(),
                    'position' => $selection->getPosition(),
                    'is_default' => $selection->getIsDefault(),
                    'selection_price_type' => $selection->getSelectionPriceType(),
                    'selection_price_value' => $selection->getSelectionPriceValue(),
                    'selection_qty' => $selection->getSelectionQty(),
                    'selection_can_change_qty' => $selection->getSelectionCanChangeQty(),
                    'delete' => ''
                );
            }
            $i++;
        }

        $newProduct->setBundleOptionsData($optionRawData);
        $newProduct->setBundleSelectionsData($selectionRawData);
        return $this;
    }

    /**
     * Setting attribute tab block for bundle
     *
     * @param Magento_Object $observer
     * @return Magento_Bundle_Model_Observer
     */
    public function setAttributeTabBlock($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == Magento_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            Mage::helper('Magento_Adminhtml_Helper_Catalog')
                ->setAttributeTabBlock('Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes');
        }
        return $this;
    }

    /**
     * Initialize product options renderer with bundle specific params
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Bundle_Model_Observer
     */
    public function initOptionRenderer(Magento_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        $block->addOptionsRenderCfg('bundle', 'Magento_Bundle_Helper_Catalog_Product_Configuration');
        return $this;
    }
}
