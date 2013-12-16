<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Adminhtml\Rma\Edit\Tab;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class Items
 * Return Items block
 *
 * @package Magento\Rma\Test\Block\Adminhtml\Rma\Edit\Tab
 */
class Items extends Form
{
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
     * Product fields
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
     * Product actions
     *
     * @var array
     */
    protected $productActions = array(
        'AUTHORIZE_QTY' => 'AUTHORIZE_QTY',
        'RETURN_QTY' => 'RETURN_QTY',
        'APPROVE_QTY' => 'APPROVE_QTY'
    );

    /**
     * Product quantity fields
     *
     * @var array
     */
    protected $productQuantities = array(
        'AUTHORIZE_QTY' => "//input[contains(@name,'qty_authorized')]",
        'RETURN_QTY' => "//input[contains(@name,'qty_returned')]",
        'APPROVE_QTY' => "//input[contains(@name,'qty_approved')]"
    );

    /**
     * Product status values
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
     * Fill form fields
     *
     * @param \Magento\Rma\Test\Fixture\ReturnItem $returnItemFixture
     * @param string $fillFields
     * @return null
     */
    public function fillCustom($returnItemFixture, $fillFields)
    {
        $products = $returnItemFixture->getProductNames();
        foreach($products as $product)
        {
            $quantity = $returnItemFixture->getQuantity();
            if(isset($this->productActions[$fillFields])) {
                $quantitySearchString = $this->productRow . $this->productQuantities[$fillFields];
                $status = $this->productStatus[$fillFields];
            }

            else {
                return null;
            }
            $quantitySearchString = sprintf($quantitySearchString, $product);
            $statusSearchString = $this->productRow . $this->statusField;
            $statusSearchString = sprintf($statusSearchString, $product);
            $this->_rootElement->find($quantitySearchString, Locator::SELECTOR_XPATH)->setValue($quantity);
            $this->_rootElement->find($statusSearchString, Locator::SELECTOR_XPATH,'select')->setValue($status);
        }
    }

    /**
     * Checks if all products from the order are in the return grid
     *
     * @param array $products
     * @param \Magento\Rma\Test\Fixture\ReturnItem $returnItem
     * @return bool
     */
    public function assertProducts($products, $returnItem)
    {
        $result = true;
        foreach ($products as $product)
        {
            $productName = $product->getProductName();

            $productSearchString = $this->productRow . $this->productNameField;
            $productSearchString = sprintf($productSearchString, $productName);
            $gridProductName = $this->_rootElement->find($productSearchString, Locator::SELECTOR_XPATH)->getText();
            if(strpos($gridProductName, $productName) === false) {
                $result = false;
            }

            $returnItemFields = $returnItem->getData('fields');

            foreach ($returnItemFields as $returnItemField => $returnItemValue)
            {
                if (isset($this->productField[$returnItemField])) {
                    $searchString = sprintf($this->productRow . $this->productField[$returnItemField], $productName);
                    $itemValue = $this->_rootElement->find($searchString, Locator::SELECTOR_XPATH)->getText();
                    if(strpos($itemValue, $returnItemValue) === false) {
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
