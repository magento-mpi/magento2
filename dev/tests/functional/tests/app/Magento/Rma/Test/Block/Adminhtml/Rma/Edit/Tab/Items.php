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

use Mtf\Block\Block;
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
     * Constant for authorized quantity fields
     *
     * @var string
     */
    public static $AUTHORIZE_QTY = 'AUTHORIZE_QTY';

    /**
     * Constant for Returned quantity fields
     *
     * @var string
     */
    public static $RETURN_QTY = 'RETURN_QTY';

    /**
     * Constant for Returned quantity fields
     *
     * @var string
     */
    public static $APPROVE_QTY = 'APPROVE_QTY';

    /**
     * Row containing product name
     *
     * @var string
     */
    protected $productRow = "//tr[contains(normalize-space(td/text()),'%s')]";

    /**
     * Status Field
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
     * @param array $products
     * @param integer $quantity
     */
    public function fillCustom($products, $quantity, $fillFields)
    {
        foreach($products as $product)
        {
            $productName = $product->getData('fields/name');
            $productName = $productName['value'];

            if (Items::$AUTHORIZE_QTY === $fillFields) {
                $quantitySearchString = $this->productRow . $this->quantityAuthorizedField;
                $status = $this->statusAuthorized;
            }
            elseif (Items::$RETURN_QTY == $fillFields) {
                $quantitySearchString = $this->productRow . $this->quantityReturnedField;
                $status = $this->statusReturnReceived;
            }
            elseif (Items::$APPROVE_QTY == $fillFields) {
                $quantitySearchString = $this->productRow . $this->quantityApprovedField;
                $status = $this->statusApproved;
            }
            else {
                return null;
            }
            $quantitySearchString = sprintf($quantitySearchString, $productName);
            $statusSearchString = $this->productRow . $this->statusField;
            $statusSearchString = sprintf($statusSearchString, $productName);
            $this->_rootElement->find($quantitySearchString, Locator::SELECTOR_XPATH)->setValue($quantity);
            $this->_rootElement->find($statusSearchString, Locator::SELECTOR_XPATH,'select')->setValue($status);
        }
    }
}
