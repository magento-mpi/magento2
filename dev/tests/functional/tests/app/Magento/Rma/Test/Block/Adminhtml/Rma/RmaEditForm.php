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

namespace Magento\Rma\Test\Block\Adminhtml\Rma;

use Mtf\Block\Block;
use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class Returns
 * Order Returns block
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order
 */
class RmaEditForm extends Form
{
    protected $productRow = "//tr[contains(normalize-space(td/text()),'%s')]";

    protected $statusField = "//select[contains(@name,'status')]";

    protected $quantityAuthorizedField = "//input[contains(@name,'qty_authorized')]";

    protected $quantityReturnedField = "//input[contains(@name,'qty_returned')]";

    protected $quantityApprovedField = "//input[contains(@name,'qty_approved')]";

    protected $statusAuthorized = 'Authorize';

    protected $statusReturnReceived = 'Return Received';

    protected $statusApproved = 'Approved';


    /**

    /**
     * {@inheritdoc}
     */
    protected $filters = array(
        'id' => array(
            'selector' => '#order_rma_filter_increment_id_to'
        ),
    );

    /**
     * Fill form with Authorization fields
     *
     */
    public function fillAuthorized($products, $quantity)
    {
        foreach($products as $product)
        {
            $productName = $product->getData('fields/name');
            $productName = $productName['value'];
            $quantitySearchString = $this->productRow . $this->quantityAuthorizedField;
            $quantitySearchString = sprintf($quantitySearchString, $productName);
            $statusSearchString = $this->productRow . $this->statusField;
            $statusSearchString = sprintf($statusSearchString, $productName);
            $this->_rootElement->find($quantitySearchString, Locator::SELECTOR_XPATH)->setValue($quantity);
            $this->_rootElement->find($statusSearchString, Locator::SELECTOR_XPATH,'select')->setValue($this->statusAuthorized);
        }
    }

    /**
     * Fill form with Returned fields
     *
     */
    public function fillReturned($products, $quantity)
    {
        foreach($products as $product)
        {
            $productName = $product->getData('fields/name');
            $productName = $productName['value'];
            $quantitySearchString = $this->productRow . $this->quantityReturnedField;
            $quantitySearchString = sprintf($quantitySearchString, $productName);
            $statusSearchString = $this->productRow . $this->statusField;
            $statusSearchString = sprintf($statusSearchString, $productName);
            $this->_rootElement->find($quantitySearchString, Locator::SELECTOR_XPATH)->setValue($quantity);
            $this->_rootElement->find($statusSearchString, Locator::SELECTOR_XPATH,'select')->setValue($this->statusReturnReceived);
        }
    }

    /**
     * Fill form with Approved fields
     *
     */
    public function fillApproved($products, $quantity)
    {
        foreach($products as $product)
        {
            $productName = $product->getData('fields/name');
            $productName = $productName['value'];
            $quantitySearchString = $this->productRow . $this->quantityApprovedField;
            $quantitySearchString = sprintf($quantitySearchString, $productName);
            $statusSearchString = $this->productRow . $this->statusField;
            $statusSearchString = sprintf($statusSearchString, $productName);
            $this->_rootElement->find($quantitySearchString, Locator::SELECTOR_XPATH)->setValue($quantity);
            $this->_rootElement->find($statusSearchString, Locator::SELECTOR_XPATH,'select')->setValue($this->statusApproved);
        }
    }
}
