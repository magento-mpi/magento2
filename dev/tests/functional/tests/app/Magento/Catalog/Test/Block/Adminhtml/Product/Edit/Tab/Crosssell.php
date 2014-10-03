<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Crosssell\Grid as CrosssellGrid;

/**
 * Class Crosssell
 * Cross-sell Tab
 */
class Crosssell extends Tab
{
    /**
     * Locator for cross sell products grid
     *
     * @var string
     */
    protected $crossSellGrid = '#cross_sell_product_grid';

    /**
     * Select cross-sells products
     *
     * @param array $data
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $data, Element $element = null)
    {
        if (isset($data['cross_sell_products']['value'])) {
            $context = $element ? $element : $this->_rootElement;
            /** @var CrosssellGrid $crossSellBlock */
            $crossSellBlock = $this->blockFactory->create(
                '\Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Crosssell\Grid',
                ['element' => $context->find($this->crossSellGrid)]
            );

            foreach ($data['cross_sell_products']['value'] as $product) {
                $crossSellBlock->searchAndSelect(['sku' => $product['sku']]);
            }
        }

        return $this;
    }

    /**
     * Get data of tab
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $context = $element ? $element : $this->_rootElement;
        /** @var CrosssellGrid $crossSellBlock */
        $crossSellBlock = $this->blockFactory->create(
            '\Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Crosssell\Grid',
            ['element' => $context->find($this->crossSellGrid)]
        );
        $columns = [
            'entity_id',
            'name',
            'sku',
        ];
        $crossSellProducts = $crossSellBlock->getRowsData($columns);

        return ['cross_sell_products' => $crossSellProducts];
    }
}
