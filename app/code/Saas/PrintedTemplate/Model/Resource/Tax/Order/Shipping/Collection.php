<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Detailed tax infoirmation for order shipping
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Resource_Tax_Order_Shipping_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection constructor.
     * Initialize collection's model.
     */
    protected function _construct()
    {
        $this->_init(
            'Saas_PrintedTemplate_Model_Tax_Order_Shipping',
            'Saas_PrintedTemplate_Model_Resource_Tax_Order_Shipping'
        );
    }

    /**
     * Adds percent, real_percent, priority, item_id columns
     * and adds sorting by priority and percent
     *
     * @see Magento_Core_Model_Resource_Db_Collection_Abstract::_initSelect()
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addFieldToSelect('percent')
            ->addFieldToSelect('real_percent')
            ->addFieldToSelect('priority')
            ->addFieldToSelect('is_tax_after_discount')
            ->addFieldToSelect('is_discount_on_incl_tax')
            ->addExpressionFieldToSelect('item_id', '("shipping")', '')
            ->setOrder('priority', Magento_Data_Collection::SORT_ORDER_ASC)
            ->setOrder('real_percent', Magento_Data_Collection::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Add filter by order and order_id, tax_amount, base_tax_amount columns
     *
     * @param Mage_Sales_Model_Order $order
     * @return Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_Collection Self
     */
    public function addFilterByOrder(Mage_Sales_Model_Order $order)
    {
        $this->join(
            'sales_flat_order',
            '`sales_flat_order`.entity_id = main_table.order_id',
            array(
                'order_id'             => 'entity_id',
                'row_total'            => 'shipping_amount',
                'base_row_total'       => 'base_shipping_amount',
                'discount_amount'      => 'shipping_discount_amount',
                'base_discount_amount' => 'base_shipping_discount_amount',
                'tax_amount'           => 'shipping_tax_amount',
                'base_tax_amount'      => 'base_shipping_tax_amount',
            )
        );
        $this->addFieldToFilter('main_table.order_id', $order->getId());

        return $this;
    }

    /**
     * Add filter by invoice; joins invoice and order tables;
     * adds row_total, base_row_total, tax_amount, base_tax_amount,
     * discount_amount, base_discount_amount columns
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_Collection Self
     */
    public function addFilterByInvoice(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $this->join(
            'sales_flat_invoice',
            'main_table.order_id = `sales_flat_invoice`.order_id AND `sales_flat_invoice`.shipping_amount > 0.00001',
            array(
                'row_total'      => 'shipping_amount',
                'base_row_total' => 'base_shipping_amount',
                'tax_amount'     => 'shipping_tax_amount',
                'base_tax_amount' => 'base_shipping_tax_amount',
            )
        );
        $this->join(
            'sales_flat_order',
            '`sales_flat_order`.entity_id = `sales_flat_invoice`.order_id',
            array(
                'discount_amount'      => 'shipping_discount_amount',
                'base_discount_amount' => 'base_shipping_discount_amount',
            )
        );
        $this->addFieldToFilter('sales_flat_invoice.entity_id', $invoice->getId());

        return $this;
    }

    /**
     * Add filter by creditmemo; joins creditmemo and order tables;
     * adds row_total, base_row_total, tax_amount, base_tax_amount,
     * discount_amount, base_discount_amount columns
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_Collection Self
     */
    public function addFilterByCreditmemo(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $this->join(
            'sales_flat_creditmemo',
            implode(
                ' AND ',
                array(
                    'main_table.order_id = `sales_flat_creditmemo`.order_id',
                    '`sales_flat_creditmemo`.shipping_amount > 0.00001'
                )
            ),
            array(
                'row_total'      => 'shipping_amount',
                'base_row_total' => 'base_shipping_amount',
                'tax_amount'     => 'shipping_tax_amount',
                'base_tax_amount' => 'base_shipping_tax_amount'
            )
        );
        $this->join(
            'sales_flat_order',
            '`sales_flat_order`.entity_id = `sales_flat_creditmemo`.order_id',
            array(
                'discount_amount'      => 'shipping_discount_amount',
                'base_discount_amount' => 'base_shipping_discount_amount',
            )
        );
        $this->addFieldToFilter('sales_flat_creditmemo.entity_id', $creditmemo->getId());

        return $this;
    }
}
