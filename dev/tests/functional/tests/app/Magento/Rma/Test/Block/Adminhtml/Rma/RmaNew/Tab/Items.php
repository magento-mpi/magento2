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
use Magento\Rma\Test\Fixture\ReturnItem;
use Magento\Rma\Test\Block\Adminhtml\Rma\RmaNew\Tab\Items\Order\Grid as OrderItemsGrid;
use Magento\Rma\Test\Block\Adminhtml\Rma\RmaNew\Tab\Items\Grid as ItemsGrid;

/**
 * Class Items
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
     * Magento loader.
     *
     * @var string
     */
    protected $loader = './/ancestor::body/div[@class="loading-mask"]';

    /**
     * Row containing product name.
     *
     * @var string
     */
    protected $productRow = "//tr[contains(normalize-space(td/text()),'%s')]";

    /**
     * Product name field.
     *
     * @var string
     */
    protected $productNameField = "//td[contains(@class, 'col-product col-product_admin_name')]";

    /**
     * Status Field.
     *
     * @var string
     */
    protected $statusField = "//select[contains(@name,'status')]";

    /**
     * Product fields.
     *
     * @var array
     */
    protected $productField = array(
        'quantity' => "//td[contains(@class, 'col-qty col-qty_requested')]",
        'reason' => "//td[contains(@class, 'col-reason col-reason')]",
        'condition' => "//td[contains(@class, 'col-condition col-condition')]",
        'resolution' => "//td[contains(@class, 'col-resolution col-resolution')]"
    );

    /**
     * Product actions.
     *
     * @var array
     */
    protected $productActions = array(
        'AUTHORIZE_QTY' => 'AUTHORIZE_QTY',
        'RETURN_QTY' => 'RETURN_QTY',
        'APPROVE_QTY' => 'APPROVE_QTY'
    );

    /**
     * Product quantity fields.
     *
     * @var array
     */
    protected $productQuantities = array(
        'AUTHORIZE_QTY' => "//input[contains(@name,'qty_authorized')]",
        'RETURN_QTY' => "//input[contains(@name,'qty_returned')]",
        'APPROVE_QTY' => "//input[contains(@name,'qty_approved')]"
    );

    /**
     * Product status values.
     *
     * @var array
     */
    protected $productStatus = array(
        'AUTHORIZE_QTY' => 'Authorize',
        'RETURN_QTY' => 'Return Received',
        'APPROVE_QTY' => 'Approved'
    );

    /**
     * {@inheritdoc}
     */
    protected $filters = array(
        'id' => array(
            'selector' => '#order_rma_filter_increment_id_to'
        ),
    );

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
            $this->waitForElementVisible($this->orderItemsGrid);
            foreach ($items as $item) {
                $this->getOrderItemsGrid()->selectItem($item['product']);
            }

            $this->clickAddSelectedProducts();
            $this->waitForElementNotVisible($this->loader, Locator::SELECTOR_XPATH);
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
    }

    /**
     * Click "Add Selected Product(s) to returns" button
     *
     * @return void.
     */
    protected function clickAddSelectedProducts()
    {
        $this->_rootElement->find($this->addSelectedProducts)->click();
        $this->waitForElementNotVisible($this->loader, Locator::SELECTOR_XPATH);
    }

    /**
     * Return product grid.
     *
     * @return OrderItemsGrid
     */
    protected function getOrderItemsGrid()
    {
        return $this->blockFactory->create(
            'Magento\Rma\Test\Block\Adminhtml\Rma\RmaNew\Tab\Items\Order\Grid',
            ['element' => $this->_rootElement]
        );
    }

    /**
     * Return product grid.
     *
     * @return ItemsGrid
     */
    protected function getItemsGrid()
    {
        return $this->blockFactory->create(
            'Magento\Rma\Test\Block\Adminhtml\Rma\RmaNew\Tab\Items\Grid',
            ['element' => $this->_rootElement]
        );
    }

    /**
     * Fill item in rma items grid
     *
     * @param array $itemData
     * @return void
     */
    protected function fillItem(array $itemData)
    {
        /** @var CatalogProductSimple $product */        $product = $itemData['product'];
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

    /**
     * Fill form fields.
     *
     * @param \Magento\Rma\Test\Fixture\ReturnItem $returnItemFixture
     * @param string $fillFields
     * @return null
     */
    public function fillCustom($returnItemFixture, $fillFields)
    {
        $products = $returnItemFixture->getProductNames();
        foreach ($products as $product) {
            $quantity = $returnItemFixture->getQuantity();
            if (isset($this->productActions[$fillFields])) {
                $quantitySearchString = $this->productRow . $this->productQuantities[$fillFields];
                $status = $this->productStatus[$fillFields];
            } else {
                return null;
            }
            $quantitySearchString = sprintf($quantitySearchString, $product);
            $statusSearchString = $this->productRow . $this->statusField;
            $statusSearchString = sprintf($statusSearchString, $product);
            $this->_rootElement->find($quantitySearchString, Locator::SELECTOR_XPATH)->setValue($quantity);
            $this->_rootElement->find($statusSearchString, Locator::SELECTOR_XPATH, 'select')->setValue($status);
        }
    }

    /**
     * Checks if all products from the order are in the return grid.
     *
     * @param array $products
     * @param ReturnItem $returnItem
     * @return bool
     * @throws \Exception
     */
    public function assertProducts($products, $returnItem)
    {
        $result = true;
        foreach ($products as $product) {
            $productName = $product->getName();

            $productSearchString = $this->productRow . $this->productNameField;
            $productSearchString = sprintf($productSearchString, $productName);
            $gridProductName = $this->_rootElement->find($productSearchString, Locator::SELECTOR_XPATH)->getText();
            if (strpos($gridProductName, $productName) === false) {
                $result = false;
            }

            $returnItemFields = $returnItem->getData('fields');

            foreach ($returnItemFields as $returnItemField => $returnItemValue) {
                if (isset($this->productField[$returnItemField])) {
                    $searchString = sprintf($this->productRow . $this->productField[$returnItemField], $productName);
                    $itemValue = $this->_rootElement->find($searchString, Locator::SELECTOR_XPATH)->getText();
                    if (strpos($itemValue, $returnItemValue) === false) {
                        $result = false;
                    }
                } else {
                    throw new \Exception('Product not found: ' . $productName);
                }
            }
        }
        return $result;
    }
}
