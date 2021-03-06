<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Test\Block\Adminhtml\Rma\NewRma\Tab\Items\Order;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Mtf\Fixture\FixtureInterface;

/**
 * Grid for choose order item.
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'sku' => [
            'selector' => 'th.col-sku input',
        ],
    ];

    /**
     * Select order item.
     *
     * @param FixtureInterface $product
     * @reutrn void
     */
    public function selectItem(FixtureInterface $product)
    {
        /** @var CatalogProductSimple $product */
        $productConfig = $product->getDataConfig();
        $productType = isset($productConfig['type_id']) ? ucfirst($productConfig['type_id']) : '';
        $productGridClass = 'Magento\Rma\Test\Block\Adminhtml\Rma\NewRma\Tab\Items\Order\\' . $productType . 'Grid';

        if (class_exists($productGridClass)) {
            $productGrid = $this->blockFactory->create($productGridClass, ['element' => $this->_rootElement]);
            $productGrid->selectItem($product);
        } else {
            $this->searchAndSelect(['sku' => $product->getSku()]);
        }
    }
}
