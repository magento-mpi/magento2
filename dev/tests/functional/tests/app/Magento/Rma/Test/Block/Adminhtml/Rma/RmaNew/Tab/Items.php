<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Adminhtml\Rma\RmaNew\Tab;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Rma\Test\Block\Adminhtml\Rma\RmaNew\Tab\Items\Order\Grid as OrderItemsGrid;
use Magento\Rma\Test\Block\Adminhtml\Rma\RmaNew\Tab\Items\Grid as ItemsGrid;

/**
 * Items product tab.
 */
class Items extends \Magento\Rma\Test\Block\Adminhtml\Rma\Edit\Tab\Items
{
    /**
     * Selector for "Add Products" button.
     *
     * @var string
     */
    protected $addProducts = '[title="Add Products"]';

    /**
     * Selector for "Add Selected Product(s) to returns" button.
     *
     * @var string
     */
    protected $addSelectedProducts = '[title="Add Selected Product(s) to returns"]';

    /**
     * Locator item row by name.
     *
     * @var string
     */
    protected $rowByName = './/tbody/tr[./td[contains(@class,"col-product_name") and contains(.,"%s")]]';

    /**
     * Locator for order items grid.
     *
     * @var string
     */
    protected $orderItemsGrid = '#select-order-items-block';

    /**
     * Locator for grid loader.
     *
     * @var string
     */
    protected $gridLoader = './/ancestor::body/div[@class="loading-mask"]';

    /**
     * Locator for main loader.
     *
     * @var string
     */
    protected $mainLoader = './/ancestor::body/div[@id="loading-mask"]';

    /**
     * Locator for rma items grid.
     *
     * @var string
     */
    protected $rmaItemsGrid = '#rma_items_grid';

    /**
     * Fill data to fields on tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $items = isset($fields['items']['value']) ? $fields['items']['value'] : [];

        if (!empty($items)) {
            $this->clickAddProducts();
            foreach ($items as $item) {
                $this->getOrderItemsGrid()->selectItem($item['product']);
            }

            $this->clickAddSelectedProducts();
            foreach ($items as $item) {
                $this->fillItem($item);
            }

            $this->setFields['items'] = $items;
        }

        return $this;
    }

    /**
     * Click "Add Products" button.
     *
     * @return void
     */
    protected function clickAddProducts()
    {
        $this->_rootElement->find($this->addProducts)->click();
        $this->waitForElementVisible($this->orderItemsGrid);
    }

    /**
     * Click "Add Selected Product(s) to returns" button.
     *
     * @return void.
     */
    protected function clickAddSelectedProducts()
    {
        $this->_rootElement->find($this->addSelectedProducts)->click();
        $this->waitForElementNotVisible($this->gridLoader, Locator::SELECTOR_XPATH);
        $this->waitForElementNotVisible($this->mainLoader, Locator::SELECTOR_XPATH);
    }

    /**
     * Return chooser order items grid.
     *
     * @return OrderItemsGrid
     */
    protected function getOrderItemsGrid()
    {
        return $this->blockFactory->create(
            'Magento\Rma\Test\Block\Adminhtml\Rma\RmaNew\Tab\Items\Order\Grid',
            ['element' => $this->_rootElement->find($this->orderItemsGrid)]
        );
    }

    /**
     * Return items rma grid.
     *
     * @return ItemsGrid
     */
    protected function getItemsGrid()
    {
        return $this->blockFactory->create(
            'Magento\Rma\Test\Block\Adminhtml\Rma\RmaNew\Tab\Items\Grid',
            ['element' => $this->_rootElement->find($this->rmaItemsGrid)]
        );
    }

    /**
     * Fill item product in rma items grid.
     *
     * @param array $itemData
     * @return void
     */
    protected function fillItem(array $itemData)
    {
        /** @var CatalogProductSimple $product */
        $product = $itemData['product'];
        $productConfig = $product->getDataConfig();
        $productType = isset($productConfig['type_id']) ? ucfirst($productConfig['type_id']) : '';
        $productItemsClass = 'Magento\Rma\Test\Block\Adminhtml\Rma\RmaNew\Tab\\' . $productType . 'Items';

        if (class_exists($productItemsClass)) {
            $productGrid = $this->blockFactory->create($productItemsClass, ['element' => $this->_rootElement]);
            $productGrid->fillItem($itemData);
        } else {
            unset($itemData['product']);
            $fields = $this->dataMapping($itemData);
            $itemRow = $this->getItemsGrid()->getItemRow($product);
            $this->_fill($fields, $itemRow);
        }
    }
}
