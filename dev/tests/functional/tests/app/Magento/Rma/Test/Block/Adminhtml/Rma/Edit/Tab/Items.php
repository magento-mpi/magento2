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
     * Constant for authorized quantity fields.
     *
     * @var string
     */
    public static $AUTHORIZE_QTY = 'AUTHORIZE_QTY';

    /**
     * Constant for Returned quantity fields.
     *
     * @var string
     */
    public static $RETURN_QTY = 'RETURN_QTY';

    /**
     * Constant for Returned quantity fields.
     *
     * @var string
     */
    public static $APPROVE_QTY = 'APPROVE_QTY';

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
     * Quantity requested field.
     *
     * @var string
     */
    protected $productQuantityRequestedField = "//td[contains(@class, 'col-qty col-qty_requested')]";

    /**
     * Return resolution field.
     *
     * @var string
     */
    protected $productResolutionField = "//td[contains(@class, 'col-resolution col-resolution')]";

    /**
     * Return Reason field.
     *
     * @var string
     */
    protected $productReasonField = "//td[contains(@class, 'col-reason col-reason')]";

    /**
     * Return condition field.
     *
     * @var string
     */
    protected $productConditionField = "//td[contains(@class, 'col-condition col-condition')]";

    /**
     * Status Field.
     *
     * @var string
     */
    protected $statusField = "//select[contains(@name,'status')]";

    /**
     * Quantity authorized field
     *
     * @var string
     */
    protected $quantityAuthorizedField = "//input[contains(@name,'qty_authorized')]";

    /**
     * Quantity returned field
     *
     * @var string
     */
    protected $quantityReturnedField = "//input[contains(@name,'qty_returned')]";

    /**
     * Quantity approved field
     *
     * @var string
     */
    protected $quantityApprovedField = "//input[contains(@name,'qty_approved')]";

    /**
     * Status 'Authorize'
     *
     * @var string
     */
    protected $statusAuthorized = 'Authorize';

    /**
     * Status 'Return Received'
     *
     * @var string
     */
    protected $statusReturnReceived = 'Return Received';

    /**
     * Status 'Approved'
     *
     * @var string
     */
    protected $statusApproved = 'Approved';


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
            if (Items::$AUTHORIZE_QTY === $fillFields) {
                $quantitySearchString = $this->productRow . $this->quantityAuthorizedField;
                $status = $this->statusAuthorized;
            }
            elseif (Items::$RETURN_QTY === $fillFields) {
                $quantitySearchString = $this->productRow . $this->quantityReturnedField;
                $status = $this->statusReturnReceived;
            }
            elseif (Items::$APPROVE_QTY === $fillFields) {
                $quantitySearchString = $this->productRow . $this->quantityApprovedField;
                $status = $this->statusApproved;
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
                if ($returnItemField === 'quantity') {
                    $returnQuantitySearchString = $this->productRow . $this->productQuantityRequestedField;
                    $returnQuantitySearchString = sprintf($returnQuantitySearchString, $productName);
                    $gridQuantityRequested = $this->_rootElement
                        ->find($returnQuantitySearchString, Locator::SELECTOR_XPATH)->getText();
                    if(strpos($gridQuantityRequested, $returnItemValue) === false) {
                        $result = false;
                    }
                }
                elseif ($returnItemField === 'reason') {
                    $returnReasonSearchString = $this->productRow . $this->productReasonField;
                    $returnReasonSearchString = sprintf($returnReasonSearchString, $productName);
                    $gridReason = $this->_rootElement->find($returnReasonSearchString, Locator::SELECTOR_XPATH)->getText();
                    if(strpos($gridReason, $returnItemValue) === false) {
                        $result = false;
                    }
                }
                elseif ($returnItemField === 'condition') {
                    $returnConditionSearchString = $this->productRow . $this->productConditionField;
                    $returnConditionSearchString = sprintf($returnConditionSearchString, $productName);
                    $gridCondition = $this->_rootElement
                        ->find($returnConditionSearchString, Locator::SELECTOR_XPATH)->getText();
                    if(strpos($gridCondition, $returnItemValue) === false) {
                        $result = false;
                    }
                }
                elseif ($returnItemField === 'resolution') {
                    $returnResolutionSearchString = $this->productRow . $this->productResolutionField;
                    $returnResolutionSearchString = sprintf($returnResolutionSearchString, $productName);
                    $gridResolution = $this->_rootElement
                        ->find($returnResolutionSearchString, Locator::SELECTOR_XPATH)->getText();
                    if(strpos($gridResolution, $returnItemValue) === false) {
                        $result = false;
                    }
                }
            }
        }
        return $result;
    }
}
