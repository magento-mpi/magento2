<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Block\Product\Grouped;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\InjectableFixture;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class AssociatedProducts
 * Grouped products tab
 */
class AssociatedProducts extends Tab
{
    /**
     * 'Create New Option' button
     *
     * @var Element
     */
    protected $addNewOption = '#grouped-product-container>button';

    /**
     * Associated products grid locator
     *
     * @var string
     */
    protected $productSearchGrid = "./ancestor::body//div[div[contains(@data-role,'add-product-dialog')]]";

    /**
     * Associated products list block
     *
     * @var string
     */
    protected $associatedProductsBlock = '[data-role=grouped-product-grid]';

    /**
     * Get search grid
     *
     * @return AssociatedProducts\Search\Grid
     */
    protected function getSearchGridBlock()
    {
        return $this->blockFactory->create(
            'Magento\GroupedProduct\Test\Block\Product\Grouped\AssociatedProducts\Search\Grid',
            ['element' => $this->_rootElement->find($this->productSearchGrid, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Get associated products list block
     *
     * @return AssociatedProducts\ListAssociatedProducts
     */
    protected function getListAssociatedProductsBlock()
    {
        return $this->blockFactory->create(
            'Magento\GroupedProduct\Test\Block\Product\Grouped\AssociatedProducts\ListAssociatedProducts',
            ['element' => $this->_rootElement->find($this->associatedProductsBlock)]
        );
    }

    /**
     * Fill data to fields on tab
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $groupedProducts = $this->prepareGroupedData($fields['grouped_products']['value']);
        foreach ($groupedProducts as $key => $groupedProduct) {
            $element->find($this->addNewOption)->click();
            $searchBlock = $this->getSearchGridBlock();
            $searchBlock->searchAndSelect(['sku' => $groupedProduct['search_data']['sku']]);
            $searchBlock->addProducts();
            $this->getListAssociatedProductsBlock()->fillProductOptions(['qty' => $groupedProduct['qty']], ++$key);
        }

        return $this;
    }

    /**
     * Get data to fields on group tab
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $newFields = [];
        $groupedProducts = $this->prepareGroupedData($fields['grouped_products']['value']);
        foreach ($groupedProducts as $key => $groupedProduct) {
            $newFields['grouped_products'][$key] = $this->getListAssociatedProductsBlock()
                ->getProductOptions($groupedProduct, ($key + 1));
        }

        return $newFields;
    }

    /**
     * Prepare array grouped products
     *
     * @param array $fields
     * @return array|null
     */
    protected function prepareGroupedData(array $fields)
    {
        if (!isset($fields['preset'])) {
            return $fields;
        }
        $preset = $fields['preset']['assigned_products'];
        $products = $fields['products'];
        foreach ($preset as $productIncrement => & $item) {
            if (!isset($products[$productIncrement])) {
                break;
            }
            /** @var InjectableFixture $fixture */
            $fixture = $products[$productIncrement];
            $item['search_data']['sku'] = $fixture->getSku();
        }

        return $preset;
    }
}
